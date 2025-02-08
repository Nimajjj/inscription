<?php

namespace App\Event;

use App\Model\User;

abstract class AEventUser implements IEvent
{
    protected ?User $user;

    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}