<?php

namespace App\Events;

use App\Models\TindakLanjutTemuan;
use Illuminate\Queue\SerializesModels;

class TindakLanjutTemuanEvent
{
    use SerializesModels;

    public $tindak_lanjut_temuan;
    public $action;
    public $note;
    public $user;

    public function __construct(TindakLanjutTemuan $tindak_lanjut_temuan, string $action, $user, ?string $note = null)
    {
        $this->tindak_lanjut_temuan = $tindak_lanjut_temuan;
        $this->action = $action;
        $this->note = $note;
        $this->user = $user;
    }
}
