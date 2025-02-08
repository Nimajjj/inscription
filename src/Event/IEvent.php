<?php

namespace App\Event;

interface IEvent
{
    public function name(): string;
}