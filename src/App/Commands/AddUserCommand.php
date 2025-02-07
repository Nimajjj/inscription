<?php

namespace App\App\Commands;

use App\App\Services\UserService;

class AddUserCommand
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function execute(array $input): array
    {
        if (empty($input['login']) || empty($input['password']) || empty($input['email'])) {
            return ['error' => 'Tous les champs sont obligatoires.'];
        }

        return $this->userService->createUser($input);
    }
}