<?php

namespace App\Factory;

use App\Model\User;
use App\VO\UID;
use DateMalformedStringException;

final class UserFactory implements IDataModelFactory {

    public function createUser(
        ?UID $uid,
        string $login,
        string $email,
        string $password,
        \DateTimeImmutable $createdAt
    ): User {
        if (empty($login) || empty($email) || empty($password)) {
            throw new \InvalidArgumentException("login, email, ou mdp vide");
        }
        if ($email !== filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email non valide");
        }

        $user = new User();
        $user->setId($uid)
            ->setLogin($login)
            ->setEmail($email)
            ->setPassword($password)
            ->setCreatedAt($createdAt);

        return $user;
    }

    /**
     * @throws DateMalformedStringException
     */
    public function create(array $data): User
    {
        return $this->createUser(
            isset($data['id']) ? new UID($data['id']) : null,
            $data['login'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable()
        );
    }
}
