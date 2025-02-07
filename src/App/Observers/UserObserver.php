<?php

namespace App\App\Observers;

use App\App\Services\EmailService;use App\App\Services\NewsService;

class UserObserver
{
    private EmailService $emailService;
    private NewsService $newsService;

    public function __construct(EmailService $emailService, NewsService $newsService)
    {
        $this->emailService = $emailService;
        $this->newsService = $newsService;
    }

    public function onUserCreated(array $user): void
    {
        // Envoyer un email de confirmation
        $this->emailService->send($user['email'], 'Confirmation d\'inscription', "Bienvenue {$user['login']}!");

        // Créer une news
        $this->newsService->create("Nouvel utilisateur inscrit : {$user['login']}");
    }

    public function onUserUpdated(array $user): void
    {
        // Envoyer un email de confirmation de mise à jour
        $this->emailService->send($user['email'], 'Mise à jour de compte', "Votre compte a été mis à jour.");
    }
}