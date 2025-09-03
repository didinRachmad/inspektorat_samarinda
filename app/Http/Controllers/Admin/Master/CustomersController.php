<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class CustomersController extends Controller
{
    // Menampilkan daftar customer
    public function index()
    {
        return view('master.customers.index');
    }

    public function data()
    {
        $activeMenu = currentMenu(); // helper dari AppServiceProvider
        $query = Customer::select('id', 'kode_customer', 'nama_toko', 'pemilik', 'alamat', 'id_pasar', 'nama_pasar', 'tipe_outlet');

        return DataTables::of($query)
            ->addIndexColumn()
            // ->addColumn('nama', fn ($row) => $row->nama)
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('master_customers.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('master_customers.destroy', $row->id))
            ->make(true);
    }

    // Menampilkan form untuk membuat customer baru
    public function create()
    {
        return view('master.customers.create');
    }

    // Menyimpan customer baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_customer' => [
                'required',
                Rule::unique('customers')->whereNull('deleted_at'),
            ],
            'nama_toko' => 'required',
            'alamat' => 'required',
            'pemilik' => 'nullable|string|max:255',
            'id_pasar' => 'required|integer',
            'nama_pasar' => 'required',
            'tipe_outlet' => ['required', Rule::in(['retail', 'grosir'])],
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::withTrashed()->firstOrCreate(
                ['kode_customer' => $request->kode_customer],
                $request->only(['nama_toko', 'alamat', 'pemilik', 'id_pasar', 'nama_pasar', 'tipe_outlet'])
            );

            if ($customer->trashed()) {
                $customer->restore();
            }

            DB::commit();
            return redirect()->route('master_customers.index')->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Customer Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit(Customer $customer)
    {
        return view('master.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'kode_customer' => [
                'required',
                Rule::unique('customers')->ignore($customer->id)->whereNull('deleted_at')
            ],
            'nama_toko' => 'required',
            'alamat' => 'required',
            'pemilik' => 'nullable|string|max:255',
            'id_pasar' => 'required|integer',
            'nama_pasar' => 'required',
            'tipe_outlet' => ['required', Rule::in(['retail', 'grosir'])],
        ]);

        if (
            $request->only(['kode_customer', 'nama_toko', 'alamat', 'pemilik', 'id_pasar', 'nama_pasar', 'tipe_outlet']) ==
            $customer->only(['kode_customer', 'nama_toko', 'alamat', 'pemilik', 'id_pasar', 'nama_pasar', 'tipe_outlet'])
        ) {
            return redirect()->route('master_customers.index')->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            $customer->update($request->only(['kode_customer', 'nama_toko', 'alamat', 'pemilik', 'id_pasar', 'nama_pasar', 'tipe_outlet']));
            DB::commit();
            return redirect()->route('master_customers.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Customer Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // Menghapus customer
    public function destroy(Customer $customer)
    {
        DB::beginTransaction();
        try {
            $customer->delete();
            DB::commit();
            session()->flash('success', 'Data customer berhasil dihapus.');
            return redirect()->route('master_customers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus customer: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus data.');
            return redirect()->back();
        }
    }

    public function getCustomers(Request $request)
    {
        $q = $request->get('q');

        $customers = Customer::query()
            ->when($q, function ($query, $q) {
                $query->where('kode_customer', 'like', "%{$q}%")
                    ->orWhere('nama_toko', 'like', "%{$q}%")
                    ->orWhere('id_pasar', 'like', "%{$q}%")
                    ->orWhere('nama_pasar', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'kode_customer', 'nama_toko', 'id_pasar', 'nama_pasar']);

        return response()->json(['results' => $customers]);
    }
}
