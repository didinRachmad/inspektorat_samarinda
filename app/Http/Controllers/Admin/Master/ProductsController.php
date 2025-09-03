<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;

use App\Models\Product;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class ProductsController extends Controller
{
    // Menampilkan daftar products
    public function index()
    {
        return view('master.products.index');
    }

    public function data()
    {
        $activeMenu = currentMenu(); // helper dari AppServiceProvider
        $query = Product::select('id', 'kode_produk', 'nama_produk', 'harga', 'kemasan');

        return DataTables::of($query)
            ->addIndexColumn()
            // ->addColumn('nama', fn ($row) => $row->nama)
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('master_products.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('master_products.destroy', $row->id))
            ->make(true);
    }

    // Menampilkan form untuk membuat products baru
    public function create()
    {
        return view('master.products.create');
    }

    // Menyimpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required',
            'nama_produk' => 'required',
            'harga' => 'required|numeric|min:0',
            'kemasan' => ['required', Rule::in(['Pack', 'Rtg', 'Pcs', 'Krt'])],
        ]);

        DB::beginTransaction();
        try {
            // Cari atau buat produk, termasuk yang di-soft delete
            $product = Product::withTrashed()->firstOrCreate(
                ['kode_produk' => $request->kode_produk],
                [
                    'nama_produk' => $request->nama_produk,
                    'harga' => $request->harga,
                    'kemasan' => $request->kemasan,
                ]
            );

            // Jika sebelumnya soft deleted, maka restore dan update datanya
            if ($product->trashed()) {
                $product->restore();
                $product->update([
                    'nama_produk' => $request->nama_produk,
                    'harga' => $request->harga,
                    'kemasan' => $request->kemasan,
                ]);
            }

            DB::commit();
            return redirect()->route('master_products.index')
                ->with('success', 'Produk berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Product Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan produk.');
        }
    }

    // Form edit
    public function edit(Product $product)
    {
        return view('master.products.edit', compact('product'));
    }

    // Update produk
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'kode_produk' => [
                'required',
                Rule::unique('products')
                    ->ignore($product->id)
                    ->whereNull('deleted_at'),
            ],
            'nama_produk' => 'required',
            'harga' => 'required|numeric|min:0',
            'kemasan' => ['required', Rule::in(['Pack', 'Rtg', 'Pcs', 'Krt'])],
        ]);

        // Jika tidak ada perubahan
        if (
            $request->only(['kode_produk', 'nama_produk', 'harga', 'kemasan']) ==
            $product->only(['kode_produk', 'nama_produk', 'harga', 'kemasan'])
        ) {
            return redirect()->route('master_products.index')
                ->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            // Hapus permanen jika ada soft deleted dengan kode yang sama
            $existingDeleted = Product::onlyTrashed()
                ->where('kode_produk', $request->kode_produk)
                ->first();

            if ($existingDeleted) {
                $existingDeleted->forceDelete();
                session()->flash('info', 'Produk lama yang terhapus dihapus permanen.');
            }

            $product->update([
                'kode_produk' => $request->kode_produk,
                'nama_produk' => $request->nama_produk,
                'harga' => $request->harga,
                'kemasan' => $request->kemasan,
            ]);

            DB::commit();

            return redirect()->route('master_products.index')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Product Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // Menghapus products
    public function destroy(Product $products)
    {
        DB::beginTransaction();
        try {
            $products->delete();
            DB::commit();
            session()->flash('success', 'Data products berhasil dihapus.');
            return redirect()->route('master_products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus products: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus data.');
            return redirect()->back();
        }
    }

    public function getProduct(Request $request)
    {
        $query = $request->get('q');
        $perPage = 10; // jumlah data per halaman

        $products = Product::where('nama_produk', 'like', '%' . $query . '%')
            ->select('id', 'nama_produk')
            ->paginate($perPage);

        return response()->json($products);
    }
}
