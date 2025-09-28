<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['title' => 'Master', 'route' => null, 'icon' => 'storage', 'order' => 1, 'parent_title' => null],
            ['title' => 'Irbanwil', 'route' => 'irbanwil', 'icon' => null, 'order' => 1, 'parent_title' => 'Master'],
            ['title' => 'Auditi', 'route' => 'auditi', 'icon' => null, 'order' => 2, 'parent_title' => 'Master'],
            ['title' => 'Kode Temuan', 'route' => 'kode_temuan', 'icon' => null, 'order' => 3, 'parent_title' => 'Master'],
            ['title' => 'Kode Rekomendasi', 'route' => 'kode_rekomendasi', 'icon' => null, 'order' => 4, 'parent_title' => 'Master'],
            ['title' => 'Perencanaan', 'route' => null, 'icon' => 'event_note', 'order' => 2, 'parent_title' => null],
            ['title' => 'PKPT', 'route' => 'pkpt', 'icon' => null, 'order' => 1, 'parent_title' => 'Perencanaan'],
            ['title' => 'Non PKPT', 'route' => 'non_pkpt', 'icon' => null, 'order' => 2, 'parent_title' => 'Perencanaan'],
            ['title' => 'Pelaksanaan', 'route' => null, 'icon' => 'play_circle', 'order' => 3, 'parent_title' => null],
            ['title' => 'LHP', 'route' => 'lha', 'icon' => null, 'order' => 1, 'parent_title' => 'Pelaksanaan'],
            ['title' => 'KKA', 'route' => 'kka', 'icon' => null, 'order' => 2, 'parent_title' => 'Pelaksanaan'],
            ['title' => 'Temuan', 'route' => 'temuan', 'icon' => null, 'order' => 3, 'parent_title' => 'Pelaksanaan'],
            ['title' => 'Setting', 'route' => null, 'icon' => 'settings', 'order' => 4, 'parent_title' => null],
            ['title' => 'Roles', 'route' => 'roles', 'icon' => null, 'order' => 1, 'parent_title' => 'Setting'],
            ['title' => 'Permissions', 'route' => 'permissions', 'icon' => null, 'order' => 2, 'parent_title' => 'Setting'],
            ['title' => 'Users', 'route' => 'users', 'icon' => null, 'order' => 3, 'parent_title' => 'Setting'],
            ['title' => 'Menus', 'route' => 'menus', 'icon' => null, 'order' => 4, 'parent_title' => 'Setting'],
            ['title' => 'Approval Routes', 'route' => 'approval_routes', 'icon' => null, 'order' => 5, 'parent_title' => 'Setting'],
            ['title' => 'Profile', 'route' => 'profile', 'icon' => 'manage_accounts', 'order' => 10, 'parent_title' => null],
        ];

        foreach ($menus as $menu) {
            $parentId = null;

            if ($menu['parent_title']) {
                $parent = Menu::where('title', $menu['parent_title'])->first();
                $parentId = $parent?->id;
            }

            Menu::create([
                'title'     => $menu['title'],
                'route'     => $menu['route'],
                'parent_id' => $parentId,
                'icon'      => $menu['icon'],
                'order'     => $menu['order'],
            ]);
        }
    }
}
