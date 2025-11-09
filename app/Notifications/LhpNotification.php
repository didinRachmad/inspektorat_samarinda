<?php

namespace App\Notifications;

use App\Models\Lhp;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LhpNotification extends Notification
{
    use Queueable;

    protected $lhp;
    protected $action;
    protected $note;
    protected $customMessage;

    /**
     * Buat instance notifikasi.
     */
    public function __construct(Lhp $lhp, string $action, ?string $note = null, ?string $customMessage = null)
    {
        $this->lhp = $lhp;
        $this->action = $action; // approve, reject, revise, waiting
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
            'module' => "lhp",
            'id' => $this->lhp->id,
            'nomor_lhp' => $this->lhp->nomor_lhp,
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
                return "LHP #{$this->lhp->nomor_lhp} telah disetujui dan diteruskan ke tahap berikutnya.";

            case 'reject':
                return "LHP #{$this->lhp->nomor_lhp} ditolak. Mohon periksa catatan koreksi.";

            case 'revise':
                return "LHP #{$this->lhp->nomor_lhp} perlu direvisi sesuai catatan reviewer.";

            case 'waiting':
                return "LHP #{$this->lhp->nomor_lhp} menunggu persetujuan dari pihak terkait.";

            default:
                return "Status LHP #{$this->lhp->nomor_lhp} telah diperbarui.";
        }
    }
}
