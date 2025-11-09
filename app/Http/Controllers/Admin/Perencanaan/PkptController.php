<?php

namespace App\Http\Controllers\Admin\Perencanaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pkpt\StorePkptRequest;
use App\Http\Requests\Pkpt\UpdatePkptRequest;
use App\Models\Auditi;
use App\Models\Irbanwil;
use App\Models\Mandatory;
use App\Models\Pkpt;
use App\Models\JenisPengawasan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Auth;

class PkptController extends Controller
{
    public function index()
    {
        $mandatories = Mandatory::orderBy('nama')->get();
        $auditis = Auditi::orderBy('nama_auditi')->get();
        $irbanwils = Irbanwil::orderBy('nama')->get();
        $jenisPengawasans = JenisPengawasan::with(['children' => fn($q) => $q->orderBy('urutan')])
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->get();
        return view('perencanaan.pkpt.index', compact('mandatories', 'auditis', 'irbanwils', 'jenisPengawasans'));
    }

    public function data()
    {
        $activeMenu = currentMenu();
        $request = request();

        // Subquery untuk hitung total jabatan per pkpt
        $subJabatans = DB::table('pkpt_jabatans')
            ->selectRaw('
            pkpt_id,
            SUM(CASE WHEN jabatan = "PJ" THEN jumlah ELSE 0 END) as pj,
            SUM(CASE WHEN jabatan = "WPJ" THEN jumlah ELSE 0 END) as wpj,
            SUM(CASE WHEN jabatan = "PT" THEN jumlah ELSE 0 END) as pt,
            SUM(CASE WHEN jabatan = "KT" THEN jumlah ELSE 0 END) as kt,
            SUM(CASE WHEN jabatan = "AT" THEN jumlah ELSE 0 END) as at,
            SUM(anggaran) as anggaran_total_calc
        ')
            ->groupBy('pkpt_id');

        // Query utama PKPT
        $query = Pkpt::select([
            'pkpts.id',
            'pkpts.tahun',
            'pkpts.no_pkpt',
            'mandatories.nama as mandatory_nama',
            DB::raw("CASE pkpts.bulan
                WHEN 1 THEN 'Januari' WHEN 2 THEN 'Februari' WHEN 3 THEN 'Maret'
                WHEN 4 THEN 'April' WHEN 5 THEN 'Mei' WHEN 6 THEN 'Juni'
                WHEN 7 THEN 'Juli' WHEN 8 THEN 'Agustus' WHEN 9 THEN 'September'
                WHEN 10 THEN 'Oktober' WHEN 11 THEN 'November' WHEN 12 THEN 'Desember'
            END as bulan"),
            'pkpts.ruang_lingkup',
            'pkpts.sasaran',
            'pkpts.anggaran_total',
            DB::raw("CASE pkpts.jadwal_rmp_bulan
                WHEN 1 THEN 'Januari' WHEN 2 THEN 'Februari' WHEN 3 THEN 'Maret'
                WHEN 4 THEN 'April' WHEN 5 THEN 'Mei' WHEN 6 THEN 'Juni'
                WHEN 7 THEN 'Juli' WHEN 8 THEN 'Agustus' WHEN 9 THEN 'September'
                WHEN 10 THEN 'Oktober' WHEN 11 THEN 'November' WHEN 12 THEN 'Desember'
            END as jadwal_rmp_bulan"),
            DB::raw("CASE pkpts.jadwal_rsp_bulan
                WHEN 1 THEN 'Januari' WHEN 2 THEN 'Februari' WHEN 3 THEN 'Maret'
                WHEN 4 THEN 'April' WHEN 5 THEN 'Mei' WHEN 6 THEN 'Juni'
                WHEN 7 THEN 'Juli' WHEN 8 THEN 'Agustus' WHEN 9 THEN 'September'
                WHEN 10 THEN 'Oktober' WHEN 11 THEN 'November' WHEN 12 THEN 'Desember'
            END as jadwal_rsp_bulan"),
            DB::raw("CASE pkpts.jadwal_rpl_bulan
                WHEN 1 THEN 'Januari' WHEN 2 THEN 'Februari' WHEN 3 THEN 'Maret'
                WHEN 4 THEN 'April' WHEN 5 THEN 'Mei' WHEN 6 THEN 'Juni'
                WHEN 7 THEN 'Juli' WHEN 8 THEN 'Agustus' WHEN 9 THEN 'September'
                WHEN 10 THEN 'Oktober' WHEN 11 THEN 'November' WHEN 12 THEN 'Desember'
            END as jadwal_rpl_bulan"),
            'pkpts.jadwal_hp_hari',
            'irbanwils.nama as irbanwil_nama', // ambil dari relasi irbanwils
            'jenis_pengawasans.nama as jenis_pengawasan',
            'parent_jp.nama as parent_jenis',
            'jabatans.pj',
            'jabatans.wpj',
            'jabatans.pt',
            'jabatans.kt',
            'jabatans.at',
            // Ambil semua auditi sebagai string agar sortable/searchable
            DB::raw('(SELECT GROUP_CONCAT(auditis.nama_auditi SEPARATOR ", ")
                      FROM auditis
                      JOIN auditi_pkpt ON auditis.id = auditi_pkpt.auditi_id
                      WHERE auditi_pkpt.pkpt_id = pkpts.id) as auditi_list')
        ])
            ->leftJoin('mandatories', 'mandatories.id', '=', 'pkpts.mandatory_id')
            ->leftJoin('irbanwils', 'irbanwils.id', '=', 'pkpts.irbanwil_id') // relasi baru
            ->leftJoin('jenis_pengawasans', 'jenis_pengawasans.id', '=', 'pkpts.jenis_pengawasan_id')
            ->leftJoin('jenis_pengawasans as parent_jp', 'parent_jp.id', '=', 'jenis_pengawasans.parent_id')
            ->leftJoinSub($subJabatans, 'jabatans', function ($join) {
                $join->on('pkpts.id', '=', 'jabatans.pkpt_id');
            })
            ->where('pkpts.pkpt', 1);

        // Filter
        foreach (['tahun', 'bulan', 'mandatory_id', 'irbanwil_id', 'jenis_pengawasan_id'] as $field) {
            if ($request->filled($field)) {
                if ($field == 'irbanwil_id') {
                    // Filter berdasarkan relasi irbanwil
                    $query->where('pkpts.irbanwil_id', $request->irbanwil_id);
                } else {
                    $query->where("pkpts.$field", $request->$field);
                }
            }
        }

        // Filter auditi via subquery
        if ($request->filled('auditi_id')) {
            $auditiIds = (array) $request->auditi_id;
            $query->whereExists(function ($q) use ($auditiIds) {
                $q->select(DB::raw(1))
                    ->from('auditi_pkpt')
                    ->whereColumn('auditi_pkpt.pkpt_id', 'pkpts.id')
                    ->whereIn('auditi_pkpt.auditi_id', $auditiIds);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('auditi_list', function ($row) {
                if (!$row->auditi_list) return '';
                $items = explode(',', $row->auditi_list);
                $html = '<ul class="m-0 p-0" style="list-style-type: disc; padding-left: 15px;">';
                foreach ($items as $item) {
                    $html .= '<li>' . trim($item) . '</li>';
                }
                $html .= '</ul>';
                return $html;
            })
            ->filterColumn('auditi_list', function ($query, $keyword) {
                $query->whereRaw("(SELECT GROUP_CONCAT(auditis.nama_auditi SEPARATOR ', ')
                      FROM auditis
                      JOIN auditi_pkpt ON auditis.id = auditi_pkpt.auditi_id
                      WHERE auditi_pkpt.pkpt_id = pkpts.id
                     ) LIKE ?", ["%{$keyword}%"]);
            })
            ->orderColumn('auditi_list', function ($query, $order) {
                // Sort berdasarkan auditi pertama dari GROUP_CONCAT
                $query->orderByRaw("(SELECT GROUP_CONCAT(auditis.nama_auditi SEPARATOR ', ')
                        FROM auditis
                        JOIN auditi_pkpt ON auditis.id = auditi_pkpt.auditi_id
                        WHERE auditi_pkpt.pkpt_id = pkpts.id) $order");
            })
            ->addColumn('sub_jenis_full', fn($row) => "{$row->parent_jenis}: {$row->sub_jenis}")
            ->addColumn('anggaran_total', fn($row) => (int) ($row->anggaran_total ?: $row->anggaran_total_calc))
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('pkpt.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('pkpt.destroy', $row->id))
            ->rawColumns(['ruang_lingkup', 'auditi_list'])
            ->make(true);
    }

    public function create()
    {
        $auditis = Auditi::orderBy('nama_auditi')->get();
        $mandatories = Mandatory::orderBy('nama')->get();

        // Load parent beserta children
        $jenisPengawasans = JenisPengawasan::with(['children' => fn($q) => $q->orderBy('urutan')])
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->get();

        return view('perencanaan.pkpt.create', compact('auditis', 'mandatories', 'jenisPengawasans'));
    }

    public function store(StorePkptRequest $request)
    {
        DB::beginTransaction();
        try {
            $tahun = $request->input('tahun', date('Y'));
            $bulan = $request->input('bulan', date('m'));

            $lastPkpt = Pkpt::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->where('pkpt', 1)
                ->orderBy('id', 'desc')
                ->first();

            $urutan = $lastPkpt ? $lastPkpt->id + 1 : 1;
            $no_pkpt = sprintf("PKPT-%02d-%d-%02d", $bulan, $tahun, $urutan);

            $data = $request->validated();
            $data['no_pkpt'] = $no_pkpt;
            $data['pkpt'] = 1;

            if ($request->hasFile('file_surat_tugas')) {
                $file = $request->file('file_surat_tugas');
                $filename = time() . '_' . $file->getClientOriginalName();
                $data['file_surat_tugas'] = $file->storeAs('pkpt/surat_tugas', $filename, 'public');
            }

            $pkpt = Pkpt::create($data);

            // simpan jabatans
            foreach ($request->jabatans as $jabatan) {
                $pkpt->jabatans()->create($jabatan);
            }

            // simpan auditis many-to-many
            if ($request->has('auditis')) {
                $pkpt->auditis()->sync($request->auditis);
            }

            DB::commit();
            return redirect()->route('pkpt.index')->with('success', 'Data PKPT berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error simpan PKPT: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Pkpt $pkpt)
    {
        $pkpt->load('jabatans', 'auditis');

        $auditis = Auditi::orderBy('nama_auditi')->get();
        $mandatories = Mandatory::orderBy('nama')->get();

        $jenisPengawasans = JenisPengawasan::with(['children' => fn($q) => $q->orderBy('urutan')])
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->get();

        $selectedAuditis = $pkpt->auditis->pluck('id')->toArray();

        return view('perencanaan.pkpt.edit', compact('pkpt', 'auditis', 'mandatories', 'jenisPengawasans', 'selectedAuditis'));
    }

    public function update(UpdatePkptRequest $request, Pkpt $pkpt)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->hasFile('file_surat_tugas')) {
                if ($pkpt->file_surat_tugas) Storage::disk('public')->delete($pkpt->file_surat_tugas);
                $data['file_surat_tugas'] = $request->file('file_surat_tugas')->store('pkpt/surat_tugas', 'public');
            }

            $pkpt->update($data);

            $pkpt->jabatans()->delete();
            foreach ($request->jabatans as $jabatan) {
                $pkpt->jabatans()->create($jabatan);
            }

            // update auditis many-to-many
            if ($request->has('auditis')) {
                $pkpt->auditis()->sync($request->auditis);
            } else {
                $pkpt->auditis()->detach();
            }

            DB::commit();
            return redirect()->route('pkpt.index')->with('success', 'Data PKPT berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update PKPT: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Pkpt $pkpt)
    {
        DB::beginTransaction();
        try {
            $pkpt->jabatans()->delete();
            $pkpt->delete();
            DB::commit();
            return redirect()->route('pkpt.index')->with('success', 'Data PKPT berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus PKPT: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
