<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRoute;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Role;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Str;
use Yajra\DataTables\DataTables;

class SalesOrdersController extends Controller
{
    // Menampilkan daftar sales_orders
    public function index()
    {
        return view('transaksi.sales_orders.index');
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

        // Query dasar untuk join customer dan sales_orders
        $query = SalesOrder::select([
            'sales_orders.id as id',
            'sales_orders.no_so as no_so',
            'sales_orders.tanggal as tanggal',
            'sales_orders.customer_id as customer_id',
            'sales_orders.metode_pembayaran as metode_pembayaran',
            'sales_orders.total_qty as total_qty',
            'sales_orders.total_diskon as total_diskon',
            'sales_orders.grand_total as grand_total',
            'sales_orders.approval_level as approval_level',
            'sales_orders.status as status',
            'sales_orders.keterangan as keterangan',

            'customers.kode_customer as kode_customer',
            'customers.nama_toko as nama_toko',
            DB::raw("CONCAT(customers.kode_customer, ' - ', customers.nama_toko) as customer"),
        ])
            ->join('customers', 'sales_orders.customer_id', '=', 'customers.id');

        // Filter approval khusus jika bukan sequence pertama
        if ($currentApprovalRoute && $currentApprovalRoute->sequence != 1) {
            $query = $query->where('sales_orders.approval_level', $currentApprovalRoute->sequence - 1)
                ->where('sales_orders.status', '!=', 'Rejected');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal', fn($row) => Carbon::parse($row->tanggal)->format('d/m/Y'))
            ->orderColumn('tanggal', 'tanggal $1')
            ->orderColumn('customer', function ($query, $order) {
                $query->orderByRaw("CONCAT(customers.kode_customer, ' - ', customers.nama_toko) $order");
            })
            ->filterColumn('customer', function ($query, $keyword) {
                $query->whereRaw("LOWER(CONCAT(customers.kode_customer, ' - ', customers.nama_toko)) LIKE ?", ["%" . strtolower($keyword) . "%"]);
            })

            ->addColumn('approval_level', fn($row) => $row->approval_level)
            ->addColumn('approval_sequence', fn($row) => $approvalRoute->sequence ?? 0)
            ->addColumn('status', fn($row) => $row->status)

            ->addColumn('show_url', fn($row) => route('transaksi_sales_orders.show', $row->id))
            ->addColumn('revisi_url', fn($row) => route('transaksi_sales_orders.revise', $row->id))
            ->addColumn('approve_url', fn($row) => route('transaksi_sales_orders.approve', $row->id))
            ->addColumn('reject_url', fn($row) => route('transaksi_sales_orders.reject', $row->id))

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
            ->addColumn('edit_url', fn($row) => route('transaksi_sales_orders.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('transaksi_sales_orders.destroy', $row->id))
            ->make(true);
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('customer');
        $role = Auth::user()->roles->first();
        if (!$role) {
            abort(403, 'User tidak memiliki role');
        }
        $approvalRoute = ApprovalRoute::where('module', 'transaksi_sales_orders')->where('role_id', $role->id)->first();

        return view('transaksi.sales_orders.show', compact('salesOrder', 'approvalRoute'));
    }

    public function create()
    {
        $products = Product::orderBy('id')->get();
        return view('transaksi.sales_orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'            => 'required|date',
            'customer_id'        => 'required|exists:customers,id',
            'metode_pembayaran'  => 'required|in:Tunai,Transfer',
            'detail'             => 'required|array',
            'detail.*.product_id' => 'required|exists:products,id',
        ]);

        DB::beginTransaction();
        try {
            $totalQty = 0;
            $totalDiskon = 0;
            $grandTotal = 0;

            $tanggal = Carbon::parse($request->tanggal);
            $no_so = $this->generateNoSO($tanggal);

            $salesOrder = SalesOrder::create([
                'no_so'             => $no_so,
                'tanggal'           => $request->tanggal,
                'customer_id'       => $request->customer_id,
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            foreach ($request->detail as $productId => $detail) {
                if (empty($detail['qty']) || $detail['qty'] == 0) {
                    continue;
                }

                $product = Product::findOrFail($productId); // gunakan langsung $productId
                $harga = $product->harga;
                $qty = (int) $detail['qty'];
                $subtotal = $harga * $qty;

                // Hitung diskon (contoh: diskon 5% jika qty >= 20)
                $diskon = $qty >= 20 ? $subtotal * 0.05 : 0;
                $finalSubtotal = $subtotal - $diskon;

                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id'     => $product->id,
                    'qty'            => $qty,
                    'harga'          => $harga,
                    'diskon'         => $diskon,
                    'subtotal'       => $finalSubtotal,
                ]);

                $totalQty += $qty;
                $totalDiskon += $diskon;
                $grandTotal += $finalSubtotal;
            }

            // Simpan total di sales order
            $salesOrder->update([
                'total_qty'     => $totalQty,
                'total_diskon'  => $totalDiskon,
                'grand_total'   => $grandTotal,
            ]);

            DB::commit();
            return redirect()->route('transaksi_sales_orders.index')
                ->with('success', 'Sales Order berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal simpan sales order: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // Menampilkan form untuk mengedit sales_orders
    public function edit(SalesOrder $salesOrder)
    {
        $salesOrder->load('details');
        $orderDetails = $salesOrder->details->keyBy('product_id');
        $products = Product::orderBy('id')->get();

        return view('transaksi.sales_orders.edit', compact('salesOrder', 'products', 'orderDetails'));
    }

    // Memperbarui sales_orders
    public function update(Request $request, SalesOrder $salesOrder)
    {
        $request->validate([
            'tanggal'             => 'required|date',
            'customer_id'         => 'required|exists:customers,id',
            'metode_pembayaran'   => 'required|in:Tunai,Transfer',
            'detail'              => 'required|array',
            'detail.*.product_id' => 'required|exists:products,id',
            'detail.*.qty'        => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $salesOrder->update([
                'tanggal'           => $request->tanggal,
                'customer_id'       => $request->customer_id,
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            // Ambil data detail lama untuk referensi
            $existingDetails = $salesOrder->details()->get()->keyBy('product_id');

            $totalQty = 0;
            $totalDiskon = 0;
            $grandTotal = 0;

            foreach ($request->detail as $productId => $detail) {
                $qty = (int) ($detail['qty'] ?? 0);
                if ($qty <= 0) continue;

                $product = Product::findOrFail($productId);
                $harga = $product->harga;
                $subtotal = $harga * $qty;

                $diskon = 0;
                if ($qty >= 20) {
                    $diskon = $subtotal * 0.10;
                } elseif ($qty >= 10) {
                    $diskon = $subtotal * 0.05;
                }

                $finalSubtotal = $subtotal - $diskon;

                $salesOrder->details()->updateOrCreate(
                    [
                        'sales_order_id' => $salesOrder->id,
                        'product_id'     => $product->id,
                    ],
                    [
                        'qty'      => $qty,
                        'harga'    => $harga,
                        'diskon'   => $diskon,
                        'subtotal' => $finalSubtotal,
                    ]
                );

                $totalQty += $qty;
                $totalDiskon += $diskon;
                $grandTotal += $finalSubtotal;

                // Hapus dari daftar yang masih ada agar sisanya bisa dihapus
                $existingDetails->forget($product->id);
            }

            $salesOrder->update([
                'total_qty'    => $totalQty,
                'total_diskon' => $totalDiskon,
                'grand_total'  => $grandTotal,
            ]);

            DB::commit();
            return redirect()->route('transaksi_sales_orders.index')
                ->with('success', 'Sales Order berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update sales order: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function approve(SalesOrder $salesOrder)
    {
        $activeMenu = currentMenu(); // ambil menu dari helper
        $module = Str::before($activeMenu->route, '.'); // e.g. 'transaksi_sales_orders'
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
        if ($salesOrder->approval_level != $approvalRoute->sequence - 1) {
            abort(403, 'Sales Order belum berada di tahap approval Anda.');
        }

        // Update approval level
        $salesOrder->approval_level = $approvalRoute->sequence;

        // Cek apakah masih ada approval berikutnya
        $nextApprovalRoute = ApprovalRoute::where('module', $module)
            ->where('sequence', '>', $approvalRoute->sequence)
            ->orderBy('sequence')
            ->first();

        if ($nextApprovalRoute) {
            $salesOrder->status = 'Waiting Approval';
            $salesOrder->keterangan = "Menunggu approval dari {$nextApprovalRoute->role->name}";
        } else {
            $salesOrder->status = 'Final';
            $salesOrder->keterangan = 'Final approved';
        }

        $salesOrder->save();

        session()->flash('success', 'Sales Order berhasil di-approve.');
        return redirect()->route($activeMenu->route . ".index"); // arahkan ke index sesuai modul
    }


    public function revise(SalesOrder $salesOrder, Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'keterangan' => 'nullable|string|max:255',
        ]);

        $roleName = Auth::user()->getRoleNames()->first();

        DB::beginTransaction();

        try {
            // Kembalikan ke tahap awal approval
            $salesOrder->approval_level = 0;
            $salesOrder->status = 'Revised';
            $salesOrder->keterangan = $validatedData['keterangan'] . ' | Diajukan oleh ' . $roleName;
            $salesOrder->save();

            DB::commit();
            session()->flash('success', 'data SalesOrder dikembalikan untuk Revisi.');
            return redirect()->route('transaksi_sales_orders.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat revise Sales Order: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengembalikan Sales Order untuk Revisi. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function reject(SalesOrder $salesOrder, Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'keterangan' => 'nullable|string|max:255',
        ]);

        $roleName = Auth::user()->getRoleNames()->first();

        DB::beginTransaction();

        try {
            $salesOrder->status = 'Rejected';
            $salesOrder->keterangan = $validatedData['keterangan'] . ' | Rejected oleh ' . $roleName;
            $salesOrder->save();

            DB::commit();
            session()->flash('success', 'data SalesOrder telah direject.');
            return redirect()->route('transaksi_sales_orders.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat reject Sales Order: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mereject data Sales Order. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    // Menghapus sales_orders
    public function destroy(SalesOrder $salesOrder)
    {
        DB::beginTransaction();
        try {
            $salesOrder->details()->delete();
            $salesOrder->delete();

            DB::commit();
            return redirect()->route('transaksi_sales_orders.index')
                ->with('success', 'Data Sales Order berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus Sales Order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data Sales Order.');
        }
    }

    private function generateNoSO(Carbon $tanggal)
    {
        $prefix = 'SO' . $tanggal->format('Ymd');
        $lastOrder = SalesOrder::whereDate('tanggal', $tanggal)
            ->where('no_so', 'like', $prefix . '%')
            ->orderBy('no_so', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastOrder && preg_match('/\d{9}$/', $lastOrder->no_so)) {
            $lastNumber = (int) substr($lastOrder->no_so, -3);
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // 001, 002, ...
        return $prefix . $newNumber;
    }

    public function getSalesOrders(Request $request)
    {
        $query = $request->q;
        $page  = $request->page ?: 1;
        $perPage = 10;

        $builder = SalesOrder::with('customer')
            ->where('status', 'Final')
            ->where(function ($q) use ($query) {
                $q->where('no_so', 'like', "%{$query}%")
                    ->orWhereHas('customer', function ($q2) use ($query) {
                        $q2->where('kode_customer', 'like', "%{$query}%")
                            ->orWhere('nama_toko', 'like', "%{$query}%");
                    });
            });

        $paginator = $builder->orderBy('tanggal', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $results = $paginator->getCollection()->map(function ($so) {
            return [
                'id'               => $so->id,
                'text'             => "{$so->no_so} – {$so->customer->nama_toko}",
                'no_so'            => $so->no_so,
                'kode_customer'    => $so->customer->kode_customer,
                'nama_toko'        => $so->customer->nama_toko,
                'tanggal'          => $so->tanggal->format('d/m/Y'),
                'metode_pembayaran' => $so->metode_pembayaran,
            ];
        });

        return response()->json([
            'results'    => $results,
            'pagination' => [
                'more' => $paginator->hasMorePages()
            ],
        ]);
    }

    public function getSalesOrderDetail($id)
    {
        $so = SalesOrder::with(['customer', 'details.product'])->findOrFail($id);

        return response()->json([
            'tanggal' => $so->tanggal->format('d/m/Y'),
            'metode_pembayaran' => $so->metode_pembayaran,
            'total_qty' => $so->total_qty,
            'total_diskon' => $so->total_diskon,
            'grand_total' => $so->grand_total,
            'customer' => [
                'id' => $so->customer->id,
                'kode_customer' => $so->customer->kode_customer,
                'nama_toko' => $so->customer->nama_toko,
                'alamat' => $so->customer->alamat,
                'pemilik' => $so->customer->pemilik,
                'id_pasar' => $so->customer->id_pasar,
                'nama_pasar' => $so->customer->nama_pasar,
            ],
            'details' => $so->details->map(function ($detail) {
                return [
                    'product_id' => $detail->product_id,
                    'kode_produk' => $detail->product->kode_produk,
                    'nama_produk' => $detail->product->nama_produk,
                    'kemasan' => $detail->product->kemasan,
                    'harga' => $detail->harga,
                    'qty' => $detail->qty,
                    'diskon' => $detail->diskon,
                    'subtotal' => $detail->subtotal,
                ];
            }),
        ]);
    }
}
