<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;

use App\Models\ApprovalRoute;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index()
    {
        // Ambil data menu berdasarkan route yang sesuai (misal 'Menu')
        $menu_id = Menu::where('route', 'menus')->first();

        // Pengecekan hak akses untuk melihat daftar menu
        if (!$menu_id || !Auth::user()->hasMenuPermission(
            $menu_id->id,
            'index'
        )) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar menus.');
        }

        $menus = Menu::all();
        return view('setting.menus.index', compact('menus', 'menu_id'));
    }

    public function data()
    {
        $activeMenu = currentMenu(); // helper dari AppServiceProvider
        $query = Menu::select(
            'menus.id',
            'menus.title',
            'menus.route',
            'menus.icon',
            'menus.order',
            'parents.title as parent_title'
        )->leftJoin('menus as parents', 'menus.parent_id', '=', 'parents.id');

        return DataTables::of($query)
            ->addIndexColumn()
            // ->addColumn('nama', fn ($row) => $row->nama)
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('menus.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('menus.destroy', $row->id))
            ->make(true);
    }

    public function show($id) {}

    // Di controller (method create)
    public function create()
    {
        $parentMenus = Menu::orderBy('order')->whereNull('route')->get();
        return view('setting.menus.create', compact('parentMenus'));
    }

    public function store(Request $request)
    {
        // Validasi input, tambahkan validasi untuk parent_id (nullable dan harus ada di tabel menus jika diisi)
        $validatedData = $request->validate([
            'title'     => 'required|string|max:255',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'order'     => 'required|integer',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        DB::beginTransaction();

        try {
            Menu::create([
                'title'     => $validatedData['title'],
                'route'     => $validatedData['route'],
                'icon'      => $validatedData['icon'] ?? null,
                'order'     => $validatedData['order'],
                'parent_id' => $validatedData['parent_id'] ?? null,
            ]);

            DB::commit();
            session()->flash('success', 'Data berhasil ditambahkan.');
            return redirect()->route('menus.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menambahkan menu: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menambahkan data. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function edit(Menu $menu)
    {
        $parentMenus = Menu::orderBy('order')->whereNull('route')->get();
        return view('setting.menus.edit', compact('menu', 'parentMenus'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'string|nullable|max:255',
            'order' => 'required|integer',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        DB::beginTransaction();
        try {
            $menu->update([
                'title' => $validatedData['title'],
                'route' => $validatedData['route'],
                'icon' => $validatedData['icon'],
                'order' => $validatedData['order'],
                'parent_id' => $validatedData['parent_id'] ?? null,
            ]);

            DB::commit();
            session()->flash('success', 'Data menu berhasil diperbarui.');
            return redirect()->route('menus.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui menu: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Menu $menu)
    {
        DB::beginTransaction();
        try {
            $menu->forceDelete();
            DB::commit();
            session()->flash('success', 'Data menu berhasil dihapus.');
            return redirect()->route('menus.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus menu: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }
}
