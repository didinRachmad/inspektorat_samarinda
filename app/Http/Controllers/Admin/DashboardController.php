<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TindakLanjutTemuan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tindakLanjut = collect(); // default kosong

        if ($user->hasAnyRole(['super_admin', 'auditor', 'auditi'])) {
            $tindakLanjut = TindakLanjutTemuan::query()
                ->where('is_final_approved', false)
                ->when($user->hasRole('auditi'), function ($query) use ($user) {
                    // Auditi hanya lihat miliknya sendiri
                    $query->where('auditi_id', $user->auditi_id);
                })
                ->when($user->hasRole('auditor'), function ($query) use ($user) {
                    // Auditor hanya lihat auditi di wilayahnya
                    $query->whereHas('auditi', function ($q) use ($user) {
                        $q->where('irbanwil_id', $user->irbanwil_id);
                    });
                })
                ->with([
                    'lhp:id,nomor_lhp',
                    'auditi:id,nama_auditi',
                    'temuan:id,judul_temuan,kode_temuan_id'
                ])
                ->select('id', 'lhp_id', 'temuan_id', 'created_at', 'approval_status', 'auditi_id')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($item) {
                    $deadline = Carbon::parse($item->created_at)->addDays(30);
                    $item->deadline = $deadline;
                    $item->sisa_hari = Carbon::now()->diffInDays($deadline, false);
                    return $item;
                });
        }

        return view('dashboard', compact('tindakLanjut', 'user'));
    }
}
