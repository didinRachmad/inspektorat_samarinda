<?php

namespace App\Listeners;

use App\Events\LhpEvent;
use App\Events\TindakLanjutTemuanEvent;
use App\Models\ApprovalRoute;
use App\Notifications\LhpNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendLhpNotification
{
    public function handle(LhpEvent $event)
    {
        $lhp = $event->lhp;
        $action = $event->action;

        $routes = ApprovalRoute::where('module', 'lhp')->orderBy('sequence')->get();
        $usersToNotify = collect();
        $customMessage = null;

        // ===================== APPROVE / WAITING =====================
        if ($action === 'approve' || $action === 'waiting') {
            if ($lhp->is_final_approved) {
                // ✅ Sudah final approved → kirim ke pembuat
                if ($lhp->creator) {
                    $usersToNotify->push($lhp->creator);
                }

                $customMessage = "LHP #{$lhp->nomor_lhp} telah disetujui final.";

                // =====================
                // 🔹 Buat data tindak lanjut temuan untuk setiap temuan dari LHP
                // =====================
                if ($lhp->temuans && $lhp->temuans->isNotEmpty() && $lhp->auditi) {
                    foreach ($lhp->temuans as $temuan) {
                        $tindakLanjut = \App\Models\TindakLanjutTemuan::firstOrCreate(
                            [
                                'lhp_id' => $lhp->id,
                                'temuan_id' => $temuan->id,
                                'auditi_id' => $lhp->auditi->id,
                            ],
                            [
                                'deskripsi_tindak_lanjut' => '',
                                'approval_status' => 'draft',
                                'current_approval_sequence' => null,
                                'is_final_approved' => false,
                            ]
                        );

                        // 🔔 Kirim notifikasi ke auditi
                        event(new \App\Events\TindakLanjutTemuanEvent(
                            $tindakLanjut,
                            'new_tindak_lanjut',
                            $event->user
                        ));
                    }
                }
            } else {
                // 🟡 Jika belum final → kirim ke approver berikutnya
                $nextRoute = $routes->firstWhere('sequence', $lhp->current_approval_sequence);
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
        if ($action === 'revise') {
            // Previous route
            $prevRoute = $routes->where('sequence', '<', $lhp->current_approval_sequence)
                ->sortByDesc('sequence')
                ->first();

            if ($prevRoute) {
                if ($prevRoute->assigned_user_id && $prevRoute->assignedUser) {
                    $usersToNotify->push($prevRoute->assignedUser);
                } elseif ($prevRoute->role_id && $prevRoute->role) {
                    $usersToNotify = $usersToNotify->merge($prevRoute->role->users);
                }
            } elseif ($lhp->creator) {
                // Jika kembali ke draft → notif ke pembuat
                $usersToNotify->push($lhp->creator);
            }
        }

        // ===================== REJECT =====================
        if ($action === 'reject' && $lhp->creator) {
            $usersToNotify->push($lhp->creator);
        }

        // ===================== KIRIM NOTIFIKASI =====================
        if ($usersToNotify->isNotEmpty()) {
            Notification::send(
                $usersToNotify->unique('id'),
                new LhpNotification($lhp, $action, $event->note, $customMessage)
            );
        } else {
            Log::warning('No users to notify for LHP ' . $lhp->id);
        }

        // ===================== DEBUG LOG =====================
        Log::info('DEBUG LHP notification', [
            'id' => $lhp->id,
            'action' => $action,
            'is_final_approved' => $lhp->is_final_approved,
            'current_approval_sequence' => $lhp->current_approval_sequence,
            'users' => $usersToNotify->pluck('id')->all()
        ]);
    }
}
