<?php

namespace App\Listeners;

use App\Events\TindakLanjutTemuanEvent;
use App\Models\ApprovalRoute;
use App\Notifications\TindakLanjutTemuanNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendTindakLanjutTemuanNotification
{
    public function handle(TindakLanjutTemuanEvent $event)
    {
        $tindak_lanjut_temuan = $event->tindak_lanjut_temuan;
        $action = $event->action;

        $routes = ApprovalRoute::where('module', 'tindak_lanjut_temuan')->orderBy('sequence')->get();
        $usersToNotify = collect();
        $customMessage = null;

        // ===================== NEW_TINDAK_LANJUT (DARI LHP FINAL APPROVED) =====================
        if ($action === 'new_tindak_lanjut') {
            if ($tindak_lanjut_temuan->lhp && $tindak_lanjut_temuan->lhp->auditiUsers->isNotEmpty()) {
                $usersToNotify = $tindak_lanjut_temuan->lhp->auditiUsers;
                $customMessage = "Terdapat temuan baru, mohon segera ditindaklanjuti | "
                    . "Temuan : {$tindak_lanjut_temuan->temuan->judul_temuan}.";
            }
        }

        // ===================== APPROVE / WAITING =====================
        elseif ($action === 'approve' || $action === 'waiting') {
            if ($tindak_lanjut_temuan->is_final_approved) {
                // ✅ Jika sudah final approved → kirim ke pembuat & auditi
                if ($tindak_lanjut_temuan->auditiUser) {
                    $usersToNotify->push($tindak_lanjut_temuan->auditiUser);
                }

                if ($tindak_lanjut_temuan->auditiUsers && $tindak_lanjut_temuan->auditiUsers->isNotEmpty()) {
                    $usersToNotify = $usersToNotify->merge($tindak_lanjut_temuan->auditiUsers);
                }

                $customMessage = "Tindak Lanjut Temuan #{$tindak_lanjut_temuan->nomor_tindak_lanjut_temuan} "
                    . "telah disetujui final.";
            } else {
                // 🟡 Jika belum final → kirim ke approver berikutnya
                $nextRoute = $routes->firstWhere('sequence', $tindak_lanjut_temuan->current_approval_sequence);
                if ($nextRoute) {
                    if ($nextRoute->assigned_user_id && $nextRoute->assignedUser) {
                        $usersToNotify->push($nextRoute->assignedUser);
                    } elseif ($nextRoute->role_id && $nextRoute->role) {
                        $usersToNotify = $usersToNotify->merge($nextRoute->role->users);
                    }
                }
            }
        }

        // ===================== REVISE =====================
        elseif ($action === 'revise') {
            $prevRoute = $routes->where('sequence', '<', $tindak_lanjut_temuan->current_approval_sequence)
                ->sortByDesc('sequence')
                ->first();

            if ($prevRoute) {
                if ($prevRoute->assigned_user_id && $prevRoute->assignedUser) {
                    $usersToNotify->push($prevRoute->assignedUser);
                } elseif ($prevRoute->role_id && $prevRoute->role) {
                    $usersToNotify = $usersToNotify->merge($prevRoute->role->users);
                }
            } elseif ($tindak_lanjut_temuan->auditiUser) {
                $usersToNotify->push($tindak_lanjut_temuan->auditiUser);
            }
        }

        // ===================== KIRIM NOTIFIKASI =====================
        if ($usersToNotify->isNotEmpty()) {
            Notification::send(
                $usersToNotify->unique('id'),
                new TindakLanjutTemuanNotification($tindak_lanjut_temuan, $action, $event->note, $customMessage)
            );
        } else {
            Log::warning('No users to notify for Tindak Lanjut Temuan ' . $tindak_lanjut_temuan->id);
        }

        // ===================== DEBUG LOG =====================
        Log::info('DEBUG Tindak Lanjut Temuan notification', [
            'id' => $tindak_lanjut_temuan->id,
            'action' => $action,
            'is_final_approved' => $tindak_lanjut_temuan->is_final_approved,
            'current_approval_sequence' => $tindak_lanjut_temuan->current_approval_sequence,
            'users' => $usersToNotify->pluck('id')->all()
        ]);
    }
}
