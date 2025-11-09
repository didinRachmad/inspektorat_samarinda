<?php

namespace App\Http\Controllers\Admin\Pelaksanaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kka\StoreKkaRequest;
use App\Models\Kka;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KkaController extends Controller
{
    public function store(StoreKkaRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->hasFile('file_kka')) {
                $data['file_kka'] = $request->file('file_kka')->store('kkas', 'public');
            }

            $kka = Kka::create($data);

            DB::commit();

            return redirect()
                ->route('lhp.show', $kka->lhp_id)
                ->with('success', 'KKA berhasil ditambahkan');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error simpan KKA: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    public function destroy(Kka $kka)
    {
        DB::beginTransaction();
        try {
            $lhpId = $kka->lhp_id; // simpan dulu ID induknya

            if ($kka->file_kka && Storage::disk('public')->exists($kka->file_kka)) {
                Storage::disk('public')->delete($kka->file_kka);
            }

            $kka->delete();
            DB::commit();

            return redirect()
                ->route('lhp.show', $lhpId)
                ->with('success', 'KKA berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus KKA: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus KKA.');
        }
    }
}
