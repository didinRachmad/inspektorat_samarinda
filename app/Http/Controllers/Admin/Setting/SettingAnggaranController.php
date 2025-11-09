<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggaran;
use DB;
use Illuminate\Support\Facades\Log;

class SettingAnggaranController extends Controller
{
    public function index()
    {
        $anggaran = Anggaran::first();
        return view('setting.anggaran.index', compact('anggaran'));
    }

    public function getAnggaran()
    {
        $dataAnggaran = Anggaran::first();
        $anggaran = $dataAnggaran?->anggaran ?? 170000;
        return response()->json(['anggaran' => $anggaran]);
    }

    public function update(Request $request, Anggaran $anggaran)
    {
        $validatedData = $request->validate([
            'anggaran' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $anggaran->update([
                'anggaran' => $validatedData['anggaran'],
            ]);

            DB::commit();

            session()->flash('success', 'Anggaran berhasil diperbarui.');
            return redirect()->route('setting_anggaran.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui anggaran: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui anggaran. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }
}
