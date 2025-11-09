<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\JenisPengawasan\StoreJenisPengawasanRequest;
use App\Http\Requests\JenisPengawasan\UpdateJenisPengawasanRequest;
use App\Models\JenisPengawasan;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JenisPengawasanController extends Controller
{
    public function index()
    {
        return view('master.jenis_pengawasan.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        // Ambil parent saja (parent_id null)
        $query = JenisPengawasan::whereNull('parent_id')
            ->select(['id', 'nama', 'urutan'])
            ->orderBy('urutan');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('jenis_pengawasan.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('jenis_pengawasan.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        return view('master.jenis_pengawasan.create');
    }

    public function store(StoreJenisPengawasanRequest $request)
    {
        DB::beginTransaction();
        try {
            // Simpan parent utama
            $jenis = JenisPengawasan::create($request->validated());

            // Simpan children jika ada
            if ($request->has('children')) {
                foreach ($request->children as $child) {
                    if (!empty($child['nama'])) {
                        $jenis->children()->create([
                            'nama' => $child['nama'],
                            'urutan' => $child['urutan'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('jenis_pengawasan.index')->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Jenis Pengawasan Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data!');
        }
    }

    public function edit(JenisPengawasan $jenis_pengawasan)
    {
        $jenis_pengawasan->load(['children' => fn($query) => $query->orderBy('urutan')]);

        return view('master.jenis_pengawasan.edit', compact('jenis_pengawasan'));
    }

    public function update(UpdateJenisPengawasanRequest $request, JenisPengawasan $jenis_pengawasan)
    {
        DB::beginTransaction();
        try {
            $jenis_pengawasan->update($request->validated());

            // Update children
            $existingIds = $jenis_pengawasan->children()->pluck('id')->toArray();
            $submittedIds = [];

            if ($request->has('children')) {
                foreach ($request->children as $childIndex => $child) {
                    if (!empty($child['nama'])) {
                        if (!empty($child['id']) && in_array($child['id'], $existingIds)) {
                            // Update existing child
                            $jenis_pengawasan->children()->where('id', $child['id'])->update([
                                'nama' => $child['nama'],
                                'urutan' => $child['urutan'] ?? 0,
                            ]);
                            $submittedIds[] = $child['id'];
                        } else {
                            // Create new child
                            $newChild = $jenis_pengawasan->children()->create([
                                'nama' => $child['nama'],
                                'urutan' => $child['urutan'] ?? 0,
                            ]);
                            $submittedIds[] = $newChild->id;
                        }
                    }
                }
            }

            // Hapus children yang tidak disubmit
            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                $jenis_pengawasan->children()->whereIn('id', $toDelete)->delete();
            }

            DB::commit();
            return redirect()->route('jenis_pengawasan.index')->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Jenis Pengawasan Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data!');
        }
    }

    public function destroy(JenisPengawasan $jenis_pengawasan)
    {
        DB::beginTransaction();
        try {
            // Hapus semua children jika ada
            if ($jenis_pengawasan->children()->count()) {
                $jenis_pengawasan->children()->delete();
            }

            $jenis_pengawasan->delete();
            DB::commit();

            return redirect()->route('jenis_pengawasan.index')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Jenis Pengawasan Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data!');
        }
    }
}
