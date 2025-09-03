<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mode' => 'required|string|in:tambah,edit',
            'id' => 'nullable|integer',
            'nama_pangan' => 'required|string',
            'no_kategori_pangan' => 'required|string',
            'semester' => 'required|integer',
            'tahun' => 'required|integer|between:1900,2100',

        ]);

        try {
            DB::beginTransaction();



            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Data berhasil disimpan'
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Kesalahan saat menyimpan data menu1: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
