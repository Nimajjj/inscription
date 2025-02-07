<?php

namespace App\App\Services;

use App\App\Email;

class NewsService
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function create(string $content): bool
    {
        // Simuler la création d'une news
        echo "News créée : $content\n";

        // Envoyer la news à tous les utilisateurs (sauf le nouvel utilisateur)
        $this->emailService->send('all@domain.com', 'Nouvelle news', $content);

        return true;
    }
}