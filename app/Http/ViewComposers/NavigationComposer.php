<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Models\Menu;

class NavigationComposer
{
    public function compose(View $view)
    {
        $user = auth()->user();
        $role = $user ? $user->roles->first() : null;

        // Jika tidak ada role, tampilkan kosong
        if (!$role) {
            $view->with('menuTree', collect());
            return;
        }

        // Cache berdasarkan role_id selama 6 jam
        $cacheKey = 'menu_tree_role_' . $role->id;
        $menuTree = Cache::remember($cacheKey, now()->addHours(6), function () use ($role) {
            // Ambil ID permission 'index'
            $indexPerm = Permission::where('name', 'index')->first();
            if (!$indexPerm) {
                return collect();
            }

            // Ambil daftar menu_id yang diizinkan untuk akses 'index'
            $allowedMenuIds = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->where('permission_id', $indexPerm->id)
                ->pluck('menu_id')
                ->toArray();

            // Ambil menu dasar: grup (parent_id null) atau yang diizinkan
            $baseMenus = Menu::where(function ($q) use ($allowedMenuIds) {
                $q->whereNull('parent_id')
                    ->orWhereIn('id', $allowedMenuIds);
            })
                ->where('route', '!=', 'profile')
                ->orderBy('order')
                ->get();

            // Ambil parent dari menu diizinkan agar struktur utuh
            $parentIds = $baseMenus->pluck('parent_id')->filter()->unique()->toArray();
            $parentMenus = Menu::whereIn('id', $parentIds)
                ->orderBy('order')
                ->get();

            // Gabungkan, hilangkan duplikat, lalu urutkan ulang berdasarkan order dan reset keys
            $allowed = $baseMenus->merge($parentMenus)
                ->unique('id')
                ->sortBy('order')
                ->values();

            return $this->buildMenuTree($allowed);
        });

        $view->with('menuTree', $menuTree);
    }

    protected function buildMenuTree($menus, $parentId = null)
    {
        $branch = collect();

        // Ambil semua menu dengan parent_id yang sesuai, dalam urutan array
        $items = $menus->filter(function ($menu) use ($parentId) {
            return $menu->parent_id == $parentId;
        });

        // Urutkan berdasarkan order dan reset keys
        $items = $items->sortBy('order')->values();

        foreach ($items as $menu) {
            $menu->children = $this->buildMenuTree($menus, $menu->id);
            $branch->push($menu);
        }

        return $branch;
    }
}
