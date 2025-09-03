<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRoute;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Role;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Str;
use Yajra\DataTables\DataTables;

class DeliveryOrdersController extends Controller
{
    // Menampilkan daftar delivery_orders
    public function index()
    {
        return view('transaksi.delivery_orders.index');
    }

    public function data()
    {
        $activeMenu = currentMenu(); // helper dari AppServiceProvider
        $module = Str::before($activeMenu->route, '.');
        $role = Auth::user()->roles->first();
        if (!$role) {
            abort(403, 'User tidak memiliki role');
        }

        // Ambil route approval berdasarkan modul dan role user
        $approvalRoutes = ApprovalRoute::where('module', $module)->get();
        $currentApprovalRoute = $approvalRoutes->where('role_id', $role->id)->first();

        // Query dasar untuk join customer dan delivery_orders
        $query = DeliveryOrder::select([
            'delivery_orders.id as id',
            'delivery_orders.no_do as no_do',
            'delivery_orders.tanggal as tanggal',
            'delivery_orders.sales_order_id as sales_order_id',
            'sales_orders.no_so as no_so',
            'customers.kode_customer as kode_customer',
            'customers.nama_toko as nama_toko',
            DB::raw("CONCAT(customers.kode_customer, ' - ', customers.nama_toko) as customer"),
            'sales_orders.metode_pembayaran as metode_pembayaran',
            'delivery_orders.total_qty as total_qty',
            'delivery_orders.total_diskon as total_diskon',
            'delivery_orders.grand_total as grand_total',
            'delivery_orders.approval_level as approval_level',
            'delivery_orders.status as status',
            'delivery_orders.keterangan as keterangan',
        ])
            ->join('sales_orders', 'delivery_orders.sales_order_id', '=', 'sales_orders.id')
            ->join('customers',   'sales_orders.customer_id',     '=', 'customers.id');

        // Filter approval khusus jika bukan sequence pertama
        if ($currentApprovalRoute && $currentApprovalRoute->sequence != 1) {
            $query = $query->where('delivery_orders.approval_level', $currentApprovalRoute->sequence - 1)
                ->where('delivery_orders.status', '!=', 'Rejected');
        }

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('tanggal', fn($row) => Carbon::parse($row->tanggal)->format('d/m/Y'))
            ->orderColumn('tanggal', 'tanggal $1')

            ->addColumn('approval_level', fn($row) => $row->approval_level)
            ->addColumn('approval_sequence', fn($row) => $approvalRoute->sequence ?? 0)
            ->addColumn('status', fn($row) => $row->status)

            ->addColumn('show_url', fn($row) => route('transaksi_delivery_orders.show', $row->id))
            ->addColumn('revisi_url', fn($row) => route('transaksi_delivery_orders.revise', $row->id))
            ->addColumn('approve_url', fn($row) => route('transaksi_delivery_orders.approve', $row->id))
            ->addColumn('reject_url', fn($row) => route('transaksi_delivery_orders.reject', $row->id))

            ->addColumn('can_show', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'show'))
            ->addColumn('can_print', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'print'))
            ->addColumn('can_approve', function ($row) use ($approvalRoutes) {
                return $approvalRoutes->contains(function ($route) use ($row) {
                    return $row->approval_level == $route->sequence - 1
                        && $route->role_id == Auth::user()->roles->first()->id
                        && $row->status != 'Rejected';
                });
            })
            ->addColumn('can_modify', fn($row) => $row->approval_level == 0)
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('transaksi_delivery_orders.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('transaksi_delivery_orders.destroy', $row->id))
            ->make(true);
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['sales_order.customer', 'details.product', 'shippings']); // ← tambahkan ini

        $role = Auth::user()->roles->first();
        if (!$role) {
            abort(403, 'User tidak memiliki role');
        }

        $approvalRoute = ApprovalRoute::where('module', 'transaksi_delivery_orders')
            ->where('role_id', $role->id)
            ->first();

        return view('transaksi.delivery_orders.show', compact('deliveryOrder', 'approvalRoute'));
    }

    public function create()
    {
        return view('transaksi.delivery_orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'                  => 'required|date',
            'sales_order_id'           => 'required|exists:customers,id',
            'origin'                   => 'nullable|string|max:255',
            'origin_name'              => 'nullable|string|max:255',
            'destination'              => 'nullable|string|max:255',
            'destination_name'         => 'nullable|string|max:255',
            'detail'                   => 'required|array',
            'detail.*.product_id'      => 'required|exists:products,id',
            'detail.*.qty'             => 'nullable|integer|min:0',

            'shippings'                => 'nullable|string', // validasi awal sebagai string
        ]);

        $shippings = [];
        if (!empty($validated['shippings'])) {
            $shippings = json_decode($validated['shippings'], true);


            if (!is_array($shippings)) {
                return back()->withErrors(['shippings' => 'Format data shipping tidak valid.'])->withInput();
            }

            foreach ($shippings as $i => $shipping) {
                if (
                    empty($shipping['courier_code']) ||
                    empty($shipping['courier_name']) ||
                    empty($shipping['courier_service_name']) ||
                    !isset($shipping['price'])
                ) {
                    return back()->withErrors([
                        'shippings' => "Data shipping baris ke-{$i} tidak lengkap."
                    ])->withInput();
                }
            }
        }

        DB::beginTransaction();
        try {
            $totalQty = 0;
            $totalDiskon = 0;
            $grandTotal = 0;

            $tanggal = Carbon::parse($validated['tanggal']);
            $no_do = $this->generateNoDO($tanggal);

            $deliveryOrder = DeliveryOrder::create([
                'no_do'              => $no_do,
                'tanggal'            => $validated['tanggal'],
                'sales_order_id'     => $validated['sales_order_id'],
                'origin'             => $validated['origin'] ?? "",
                'origin_name'        => $validated['origin_name'] ?? "",
                'destination'        => $validated['destination'] ?? "",
                'destination_name'   => $validated['destination_name'] ?? "",
            ]);

            foreach ($validated['detail'] as $index => $detail) {
                if (empty($detail['qty']) || $detail['qty'] == 0) continue;

                $product = Product::findOrFail($detail['product_id']);
                $harga = $product->harga;
                $qty = (int) $detail['qty'];
                $subtotal = $harga * $qty;
                $diskon = $qty >= 20 ? $subtotal * 0.05 : 0;
                $finalSubtotal = $subtotal - $diskon;

                DeliveryOrderDetail::create([
                    'delivery_order_id' => $deliveryOrder->id,
                    'product_id'        => $product->id,
                    'qty'               => $qty,
                    'harga'             => $harga,
                    'diskon'            => $diskon,
                    'subtotal'          => $finalSubtotal,
                ]);

                $totalQty += $qty;
                $totalDiskon += $diskon;
                $grandTotal += $finalSubtotal;
            }

            $deliveryOrder->update([
                'total_qty'     => $totalQty,
                'total_diskon'  => $totalDiskon,
                'grand_total'   => $grandTotal,
            ]);

            // Simpan shipping
            foreach ($shippings as $shipping) {
                $deliveryOrder->shippings()->create([
                    'courier_code'            => $shipping['courier_code'],
                    'courier_name'            => $shipping['courier_name'],
                    'courier_service_name'    => $shipping['courier_service_name'],
                    'shipment_duration_range' => $shipping['shipment_duration_range'] ?? null,
                    'price'                   => $shipping['price'],
                ]);
            }

            DB::commit();
            return redirect()->route('transaksi_delivery_orders.index')
                ->with('success', 'Delivery Order berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal simpan delivery order: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // Menampilkan form untuk mengedit delivery_orders
    public function edit(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load([
            'sales_order.customer',
            'sales_order.details.product',
            'details.product',
            'shippings',
        ]);

        $deliveryDetails = $deliveryOrder->details->keyBy('product_id');

        return view('transaksi.delivery_orders.edit', [
            'deliveryOrder' => $deliveryOrder,
            'salesOrder' => $deliveryOrder->sales_order,
            'customer' => $deliveryOrder->sales_order->customer,
            'salesOrderDetails' => $deliveryOrder->sales_order->details,
            'deliveryDetails' => $deliveryDetails,
            'shippings' => $deliveryOrder->shippings,
        ]);
    }

    // Memperbarui delivery_orders
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sales_order_id'           => 'required|exists:sales_orders,id',
            'tanggal'                  => 'required|date',
            'origin'                   => 'required|string',
            'origin_name'              => 'required|string',
            'destination'              => 'required|string',
            'destination_name'         => 'required|string',
            'detail'                   => 'required|array|min:1',
            'detail.*.product_id'      => 'required|exists:products,id',
            'detail.*.qty'             => 'required|integer|min:1',
            'detail.*.harga'           => 'required|numeric|min:0',
            'detail.*.diskon'          => 'required|numeric|min:0',
            'detail.*.subtotal'        => 'required|numeric|min:0',

            'shippings'                => 'nullable|string', // <== validasi awal sebagai string
        ]);

        // Decode dan validasi shipping manual
        $shippings = [];
        if (!empty($validated['shippings'])) {
            $shippings = json_decode($validated['shippings'], true);

            if (!is_array($shippings)) {
                return back()->withErrors(['shippings' => 'Format data shipping tidak valid.'])->withInput();
            }

            foreach ($shippings as $i => $ship) {
                if (
                    empty($ship['courier_code']) ||
                    empty($ship['courier_name']) ||
                    empty($ship['courier_service_name']) ||
                    !isset($ship['price'])
                ) {
                    return back()->withErrors([
                        'shippings' => "Data shipping baris ke-{$i} tidak lengkap."
                    ])->withInput();
                }
            }
        }

        $total_qty    = collect($validated['detail'])->sum('qty');
        $total_diskon = collect($validated['detail'])->sum('diskon');
        $grand_total  = collect($validated['detail'])->sum('subtotal');

        DB::beginTransaction();

        try {
            $deliveryOrder = DeliveryOrder::findOrFail($id);

            // Update data header
            $deliveryOrder->update([
                'sales_order_id'   => $validated['sales_order_id'],
                'tanggal'          => $validated['tanggal'],
                'origin'           => $validated['origin'],
                'origin_name'      => $validated['origin_name'],
                'destination'      => $validated['destination'],
                'destination_name' => $validated['destination_name'],
                'total_qty'        => $total_qty,
                'total_diskon'     => $total_diskon,
                'grand_total'      => $grand_total,
            ]);

            // Hapus dan simpan ulang detail produk
            $deliveryOrder->details()->delete();
            foreach ($validated['detail'] as $item) {
                $deliveryOrder->details()->create([
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'harga'      => $item['harga'],
                    'diskon'     => $item['diskon'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }

            // Hapus ongkir lama dan simpan baru
            $deliveryOrder->shippings()->delete();
            foreach ($shippings as $ship) {
                $deliveryOrder->shippings()->create([
                    'courier_code'            => $ship['courier_code'],
                    'courier_name'            => $ship['courier_name'],
                    'courier_service_name'    => $ship['courier_service_name'],
                    'shipment_duration_range' => $ship['shipment_duration_range'] ?? null,
                    'price'                   => $ship['price'],
                ]);
            }

            DB::commit();
            return redirect()->route('transaksi_delivery_orders.index')
                ->with('success', 'Delivery Order berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal update DO: ' . $th->getMessage());
            return back()
                ->withErrors(['msg' => 'Gagal menyimpan data Delivery Order.'])
                ->withInput();
        }
    }

    public function approve(DeliveryOrder $deliveryOrder)
    {
        $activeMenu = currentMenu(); // ambil menu dari helper
        $module = Str::before($activeMenu->route, '.'); // e.g. 'transaksi_delivery_orders'
        $role = Auth::user()->roles->first();

        if (!$role) {
            abort(403, 'User tidak memiliki role.');
        }

        // Ambil approval route untuk user berdasarkan module dan role
        $approvalRoute = ApprovalRoute::where('module', $module)
            ->where('role_id', $role->id)
            ->first();

        if (!$approvalRoute) {
            abort(403, 'Anda tidak memiliki hak untuk melakukan approval.');
        }

        // Pastikan user hanya bisa approve jika level approval-nya sesuai
        if ($deliveryOrder->approval_level != $approvalRoute->sequence - 1) {
            abort(403, 'Delivery Order belum berada di tahap approval Anda.');
        }

        // Update approval level
        $deliveryOrder->approval_level = $approvalRoute->sequence;

        // Cek apakah masih ada approval berikutnya
        $nextApprovalRoute = ApprovalRoute::where('module', $module)
            ->where('sequence', '>', $approvalRoute->sequence)
            ->orderBy('sequence')
            ->first();

        if ($nextApprovalRoute) {
            $deliveryOrder->status = 'Waiting Approval';
            $deliveryOrder->keterangan = "Menunggu approval dari {$nextApprovalRoute->role->name}";
        } else {
            $deliveryOrder->status = 'Final';
            $deliveryOrder->keterangan = 'Final approved';
        }

        $deliveryOrder->save();

        session()->flash('success', 'Delivery Order berhasil di-approve.');
        return redirect()->route($activeMenu->route . ".index"); // arahkan ke index sesuai modul
    }


    public function revise(DeliveryOrder $deliveryOrder, Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'keterangan' => 'nullable|string|max:255',
        ]);

        $roleName = Auth::user()->getRoleNames()->first();

        DB::beginTransaction();

        try {
            // Kembalikan ke tahap awal approval
            $deliveryOrder->approval_level = 0;
            $deliveryOrder->status = 'Revised';
            $deliveryOrder->keterangan = $validatedData['keterangan'] . ' | Diajukan oleh ' . $roleName;
            $deliveryOrder->save();

            DB::commit();
            session()->flash('success', 'data DeliveryOrder dikembalikan untuk Revisi.');
            return redirect()->route('transaksi_delivery_orders.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat revise Delivery Order: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengembalikan Delivery Order untuk Revisi. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function reject(DeliveryOrder $deliveryOrder, Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'keterangan' => 'nullable|string|max:255',
        ]);

        $roleName = Auth::user()->getRoleNames()->first();

        DB::beginTransaction();

        try {
            $deliveryOrder->status = 'Rejected';
            $deliveryOrder->keterangan = $validatedData['keterangan'] . ' | Rejected oleh ' . $roleName;
            $deliveryOrder->save();

            DB::commit();
            session()->flash('success', 'data DeliveryOrder telah direject.');
            return redirect()->route('transaksi_delivery_orders.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat reject Delivery Order: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mereject data Delivery Order. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    // Menghapus delivery_orders
    public function destroy(DeliveryOrder $deliveryOrder)
    {
        DB::beginTransaction();
        try {
            $deliveryOrder->details()->delete();
            $deliveryOrder->delete();

            DB::commit();
            return redirect()->route('transaksi_delivery_orders.index')
                ->with('success', 'Data Delivery Order berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus Delivery Order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data Delivery Order.');
        }
    }

    private function generateNoDO(Carbon $tanggal)
    {
        $prefix = 'DO' . $tanggal->format('Ymd');
        $lastOrder = DeliveryOrder::whereDate('tanggal', $tanggal)
            ->where('no_do', 'like', $prefix . '%')
            ->orderBy('no_do', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastOrder && preg_match('/\d{9}$/', $lastOrder->no_do)) {
            $lastNumber = (int) substr($lastOrder->no_do, -3);
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // 001, 002, ...
        return $prefix . $newNumber;
    }
}
