<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auditi\StoreAuditiRequest;
use App\Http\Requests\Auditi\UpdateAuditiRequest;
use App\Models\Auditi;
use App\Models\Irbanwil;
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

        $query = Auditi::with('irbanwil:id,nama')
            ->select('auditis.id', 'auditis.kode_auditi', 'auditis.nama_auditi', 'auditis.irbanwil_id', 'auditis.alamat', 'auditis.telepon');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('irbanwil', fn($row) => $row->irbanwil?->nama ?? '-')
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('auditi.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('auditi.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        $irbanwils = Irbanwil::all();
        return view('master.auditi.create', compact('irbanwils'));
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
        $irbanwils = Irbanwil::all();
        return view('master.auditi.edit', compact('auditi', 'irbanwils'));
    }

    public function update(UpdateAuditiRequest $request, Auditi $auditi)
    {
        if (
            $request->only(['kode_auditi', 'nama_auditi', 'irbanwil_id', 'alamat', 'telepon']) ==
            $auditi->only(['kode_auditi', 'nama_auditi', 'irbanwil_id', 'alamat', 'telepon'])
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
        $words = explode(' ', strtoupper($nama));

        if (count($words) >= 2) {
            return substr($words[0], 0, 3) . substr($words[1], 0, 3);
        }

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
