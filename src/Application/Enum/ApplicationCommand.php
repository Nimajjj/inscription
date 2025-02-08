<?php

namespace App\Application\Enum;

enum ApplicationCommand
{
    case UNKNOWN;
    case ADD;
    case UPDATE;
    case DELETE;
}