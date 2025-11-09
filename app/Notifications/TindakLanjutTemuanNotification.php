<?php

namespace App\Notifications;

use App\Models\TindakLanjutTemuan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TindakLanjutTemuanNotification extends Notification
{
    use Queueable;

    protected $tindak_lanjut_temuan;
    protected $action;
    protected $note;
    protected $customMessage;

    /**
     * Buat instance notifikasi.
     */
    public function __construct(TindakLanjutTemuan $tindak_lanjut_temuan, string $action, ?string $note = null, ?string $customMessage = null)
    {
        $this->tindak_lanjut_temuan = $tindak_lanjut_temuan;
        $this->action = $action; // approve, revise, waiting
        $this->note = $note;
        $this->customMessage = $customMessage;
    }

    /**
     * Channel notifikasi (pakai database saja).
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Data yang disimpan ke database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'module' => "tindak_lanjut_temuan",
            'id' => $this->tindak_lanjut_temuan->id,
            'nomor_tindak_lanjut_temuan' => $this->tindak_lanjut_temuan->lhp->nomor_lhp,
            'action' => $this->action,
            'note' => $this->note,
            'message' => $this->customMessage ?? $this->getMessage(),
        ];
    }

    /**
     * Pesan default jika tidak ada pesan khusus.
     */
    protected function getMessage(): string
    {
        switch ($this->action) {
            case 'approve':
                // Pesan umum untuk approval biasa (non-final)
                return "Tindak Lanjut Temuan #{$this->tindak_lanjut_temuan->lhp->nomor_lhp} telah disetujui dan diteruskan ke tahap berikutnya.";

            case 'revise':
                return "Tindak Lanjut Temuan #{$this->tindak_lanjut_temuan->lhp->nomor_lhp} perlu direvisi sesuai catatan reviewer.";

            case 'waiting':
                return "Tindak Lanjut Temuan #{$this->tindak_lanjut_temuan->lhp->nomor_lhp} menunggu persetujuan dari pihak terkait.";

            default:
                return "Status Tindak Lanjut Temuan #{$this->tindak_lanjut_temuan->lhp->nomor_lhp} telah diperbarui.";
        }
    }
}
