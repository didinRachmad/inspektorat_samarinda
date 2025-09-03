<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode_customer' => 'CUST001',
                'nama_toko'     => 'Toko Sumber Rejeki',
                'alamat'        => 'Jl. Raya Pasar 1, Blok A',
                'pemilik'       => 'Budi Santoso',
                'id_pasar'      => 1,
                'nama_pasar'    => 'Pasar Induk',
                'tipe_outlet'   => 'retail',
            ],
            [
                'kode_customer' => 'CUST002',
                'nama_toko'     => 'Grosir Murah Jaya',
                'alamat'        => 'Jl. Cempaka No. 17',
                'pemilik'       => 'Siti Aminah',
                'id_pasar'      => 2,
                'nama_pasar'    => 'Pasar Cempaka',
                'tipe_outlet'   => 'grosir',
            ],
            [
                'kode_customer' => 'CUST003',
                'nama_toko'     => 'Toko Amanah',
                'alamat'        => 'Jl. Melati No. 9',
                'pemilik'       => null,
                'id_pasar'      => 3,
                'nama_pasar'    => 'Pasar Melati',
                'tipe_outlet'   => 'retail',
            ],
            [
                'kode_customer' => 'CUST004',
                'nama_toko'     => 'Toko Berkah',
                'alamat'        => 'Jl. Mawar No. 15',
                'pemilik'       => 'Agus Salim',
                'id_pasar'      => 4,
                'nama_pasar'    => 'Pasar Mawar',
                'tipe_outlet'   => 'retail',
            ],
            [
                'kode_customer' => 'CUST005',
                'nama_toko'     => 'Toko Barokah',
                'alamat'        => 'Jl. Dahlia No. 6',
                'pemilik'       => 'Yusuf Hidayat',
                'id_pasar'      => 5,
                'nama_pasar'    => 'Pasar Dahlia',
                'tipe_outlet'   => 'grosir',
            ],
            [
                'kode_customer' => 'CUST006',
                'nama_toko'     => 'Grosir Sejahtera',
                'alamat'        => 'Jl. Kenanga No. 12',
                'pemilik'       => null,
                'id_pasar'      => 6,
                'nama_pasar'    => 'Pasar Kenanga',
                'tipe_outlet'   => 'grosir',
            ],
            [
                'kode_customer' => 'CUST007',
                'nama_toko'     => 'Toko Lancar Jaya',
                'alamat'        => 'Jl. Anggrek No. 8',
                'pemilik'       => 'Dewi Sartika',
                'id_pasar'      => 7,
                'nama_pasar'    => 'Pasar Anggrek',
                'tipe_outlet'   => 'retail',
            ],
            [
                'kode_customer' => 'CUST008',
                'nama_toko'     => 'Grosir Utama',
                'alamat'        => 'Jl. Merpati No. 22',
                'pemilik'       => 'Andi Saputra',
                'id_pasar'      => 8,
                'nama_pasar'    => 'Pasar Merpati',
                'tipe_outlet'   => 'grosir',
            ],
            [
                'kode_customer' => 'CUST009',
                'nama_toko'     => 'Toko Segar',
                'alamat'        => 'Jl. Cendrawasih No. 3',
                'pemilik'       => null,
                'id_pasar'      => 9,
                'nama_pasar'    => 'Pasar Cendrawasih',
                'tipe_outlet'   => 'retail',
            ],
            [
                'kode_customer' => 'CUST010',
                'nama_toko'     => 'Grosir Nusantara',
                'alamat'        => 'Jl. Nusantara No. 5',
                'pemilik'       => 'Rina Kurnia',
                'id_pasar'      => 10,
                'nama_pasar'    => 'Pasar Nusantara',
                'tipe_outlet'   => 'grosir',
            ],
        ];

        foreach ($data as $item) {
            Customer::create($item);
        }
    }
}
