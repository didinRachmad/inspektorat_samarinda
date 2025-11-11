<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Temuan;
use App\Models\TindakLanjutTemuan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tindakLanjut = collect(); // default kosong

        // --- Data tindak lanjut berdasarkan role user ---
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
                    // Hitung deadline (30 hari setelah dibuat)
                    $deadline = Carbon::parse($item->created_at)->addDays(30);
                    $item->deadline = $deadline;
                    $item->sisa_hari = Carbon::now()->diffInDays($deadline, false);
                    return $item;
                });
        }

        // --- Data progres tindak lanjut temuan (sesuai role user) ---
        $tindakLanjutFiltered = TindakLanjutTemuan::query()
            ->when($user->hasRole('auditi'), function ($query) use ($user) {
                $query->where('auditi_id', $user->auditi_id);
            })
            ->when($user->hasRole('auditor'), function ($query) use ($user) {
                $query->whereHas('auditi', function ($q) use ($user) {
                    $q->where('irbanwil_id', $user->irbanwil_id);
                });
            });

        $totalTemuan = (clone $tindakLanjutFiltered)->count();
        $totalDraft = (clone $tindakLanjutFiltered)->where('approval_status', 'draft')->count();
        $totalWaiting = (clone $tindakLanjutFiltered)->where('approval_status', 'waiting')->count();
        $totalApproved = (clone $tindakLanjutFiltered)->where('is_final_approved', true)->count();

        $persenProgres = $totalTemuan > 0
            ? round(($totalApproved / $totalTemuan) * 100, 1)
            : 0;

        $progressData = [
            'total_temuan' => $totalTemuan,
            'draft' => $totalDraft,
            'waiting' => $totalWaiting,
            'approved' => $totalApproved,
            'persen_progres' => $persenProgres,
        ];

        // --- Tampilkan ke view dashboard ---
        return view('dashboard', compact('tindakLanjut', 'progressData', 'user'));
    }
}
