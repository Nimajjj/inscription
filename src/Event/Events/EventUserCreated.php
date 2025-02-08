<?php

namespace App\Event\Events;

use App\Event\AEventUser;

class EventUserCreated extends AEventUser
{
    public function name(): string
    {
        return 'EventUserCreated';
    }
}