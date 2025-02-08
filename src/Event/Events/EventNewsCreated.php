<?php

namespace App\Event\Events;

use App\Event\AEventNews;

class EventNewsCreated extends AEventNews
{
    public function name(): string
    {
        return 'EventNewsCreated';
    }
}