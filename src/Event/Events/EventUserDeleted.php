<?php

namespace App\Event\Events;

use App\Event\AEventUser;

class EventUserDeleted extends AEventUser
{
    public function name(): string
    {
        return 'EventUserDeleted';
    }
}