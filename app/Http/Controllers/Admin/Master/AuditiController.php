<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auditi\StoreAuditiRequest;
use App\Http\Requests\Auditi\UpdateAuditiRequest;
use App\Models\Auditi;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class AuditiController extends Controller
{
    public function index()
    {
        return view('master.auditi.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();
        $query = Auditi::select('id', 'kode_auditi', 'nama_auditi', 'alamat', 'telepon');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('auditi.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('auditi.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        return view('master.auditi.create');
    }

    public function store(StoreAuditiRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['kode_auditi'] = $this->generateKode($data['nama_auditi']);

            Auditi::create($data);

            DB::commit();
            return redirect()->route('auditi.index')
                ->with('success', 'Auditi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Auditi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan auditi.');
        }
    }

    public function edit(Auditi $auditi)
    {
        return view('master.auditi.edit', compact('auditi'));
    }

    public function update(UpdateAuditiRequest $request, Auditi $auditi)
    {
        // Jika tidak ada perubahan data
        if (
            $request->only(['kode_auditi', 'nama_auditi', 'alamat', 'telepon']) ==
            $auditi->only(['kode_auditi', 'nama_auditi', 'alamat', 'telepon'])
        ) {
            return redirect()->route('auditi.index')
                ->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            $auditi->update($request->validated());

            DB::commit();
            return redirect()->route('auditi.index')
                ->with('success', 'Auditi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Auditi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(Auditi $auditi)
    {
        DB::beginTransaction();
        try {
            $auditi->delete();
            DB::commit();
            return redirect()->route('auditi.index')
                ->with('success', 'Data auditi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus auditi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    private function generateKode($nama)
    {
        // Ambil kata-kata dari nama
        $words = explode(' ', strtoupper($nama));

        // Kalau format "Dinas Pariwisata" → ambil huruf depan "Dinas" + ambil 4 huruf awal dari kata kedua
        if (count($words) >= 2) {
            return substr($words[0], 0, 3) . substr($words[1], 0, 3);
        }

        // Default ambil 6 huruf awal dari satu kata
        return substr($words[0], 0, 6);
    }

    public function getAuditi(Request $request)
    {
        $query = $request->get('q');
        $perPage = 10;

        $auditi = Auditi::where('nama_auditi', 'like', '%' . $query . '%')
            ->select('id', 'nama_auditi')
            ->paginate($perPage);

        return response()->json($auditi);
    }
}
