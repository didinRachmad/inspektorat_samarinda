<?php

namespace Database\Seeders;

use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'title'       => 'Master',
                'route'       => null,
                'parent_id'   => null,
                'icon'        => 'storage',
                'order'       => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Auditi',
                'route'       => 'auditi',
                'parent_id'   => '1',
                'icon'        => null,
                'order'       => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Perencanaan',
                'route'       => null,
                'parent_id'   => null,
                'icon'        => 'event_note',
                'order'       => 2,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'PKPT',
                'route'       => 'pkpt',
                'parent_id'   => '3',
                'icon'        => null,
                'order'       => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Pelaksanaan',
                'route'       => null,
                'parent_id'   => null,
                'icon'        => 'play_circle',
                'order'       => 2,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'LHA',
                'route'       => 'lha',
                'parent_id'   => '5',
                'icon'        => null,
                'order'       => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'KKA',
                'route'       => 'kka',
                'parent_id'   => '5',
                'icon'        => null,
                'order'       => 2,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Temuan',
                'route'       => 'temuan',
                'parent_id'   => '5',
                'icon'        => null,
                'order'       => 3,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Setting',
                'route'       => null,
                'parent_id'   => null,
                'icon'        => 'settings',
                'order'       => 4,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Roles',
                'route'       => 'roles',
                'parent_id'   => '9',
                'icon'        => null,
                'order'       => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Permissions',
                'route'       => 'permissions',
                'parent_id'   => '9',
                'icon'        => null,
                'order'       => 2,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Users',
                'route'       => 'users',
                'parent_id'   => '9',
                'icon'        => null,
                'order'       => 3,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Menus',
                'route'       => 'menus',
                'parent_id'   => '9',
                'icon'        => null,
                'order'       => 4,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Approval Routes',
                'route'       => 'approval_routes',
                'parent_id'   => '9',
                'icon'        => null,
                'order'       => 5,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'title'       => 'Profile',
                'route'       => 'profile',
                'parent_id'   => null,
                'icon'        => 'manage_accounts',
                'order'       => 10,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ];

        Menu::insert($menus);
    }
}
