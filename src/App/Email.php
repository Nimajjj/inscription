<?php

namespace App\App;

class Email
{
    public function send(string $to, string $subject, string $body): bool
    {
        // Simuler l'envoi d'un email
        echo "Email envoyé à $to : $subject\n";
        return true;
    }
}