<?php

namespace App\Event\Events;

use App\Event\AEventUser;

class EventUserUpdated extends AEventUser
{
    public function name(): string
    {
        return 'EventUserUpdated';
    }
}
