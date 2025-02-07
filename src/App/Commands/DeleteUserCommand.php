<?php

namespace App\App\Commands;

use App\App\Services\UserService;

class DeleteUserCommand
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function execute(array $input): array
    {
        if (empty($input['id'])) {
            return ['error' => 'ID utilisateur manquant.'];
        }

        return $this->userService->deleteUser($input['id']);
    }
}