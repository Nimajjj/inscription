<?php

namespace App\App\Services;

use App\App\Email;

class EmailService
{
    private Email $email;

    public function __construct()
    {
        $this->email = new Email();
    }

    public function send(string $to, string $subject, string $body): bool
    {
        return $this->email->send($to, $subject, $body);
    }
}