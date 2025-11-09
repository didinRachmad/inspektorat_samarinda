<?php

namespace App\Events;

use App\Models\Lhp;
use Illuminate\Queue\SerializesModels;

class LhpEvent
{
    use SerializesModels;

    public $lhp;
    public $action;
    public $note;
    public $user;

    public function __construct(Lhp $lhp, string $action, $user, ?string $note = null)
    {
        $this->lhp = $lhp;
        $this->action = $action;
        $this->note = $note;
        $this->user = $user;
    }
}
