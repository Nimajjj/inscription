<?php

namespace App\App\Services;

use App\App\Observers\UserObserver;

class UserService
{
    private array $observers = [];

    public function attach(UserObserver $observer)
    {
        $this->observers[] = $observer;
    }

    public function createUser(array $data): array
    {
        // Simuler la création d'un utilisateur
        $user = [
            'id' => uniqid(),
            'login' => $data['login'],
            'email' => $data['email'],
            'createdAt' => date('Y-m-d H:i:s')
        ];

        // Notifier les observateurs
        foreach ($this->observers as $observer) {
            $observer->onUserCreated($user);
        }

        return $user;
    }

    public function updateUser(string $id, array $data): array
    {
        // Simuler la mise à jour d'un utilisateur
        $user = [
            'id' => $id,
            'login' => $data['login'] ?? 'user',
            'email' => $data['email'] ?? 'email@domain.com'
        ];

        // Notifier les observateurs
        foreach ($this->observers as $observer) {
            $observer->onUserUpdated($user);
        }

        return ['status' => 'OK'];
    }

    public function deleteUser(string $id): array
    {
        // Simuler la suppression d'un utilisateur
        return ['status' => 'OK'];
    }
}
