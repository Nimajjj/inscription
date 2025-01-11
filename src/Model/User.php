<?php

namespace App\Model;

use App\VO\UID;

final class User
{
    private ?UID $id;
    private string $login;
    private string $email;
    private string $password;
    private \DateTimeImmutable $createdAt;
    
    public function __construct(
        ?UID $id = null,
        string $login = '',
        string $email = '',
        string $password = '',
        ?\DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id ?: new UID();
        $this->login = $login;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt ?: new \DateTimeImmutable();
    }

    // === Getters et Setters ===

    public function getId(): ?UID
    {
        return $this->id;
    }

    public function setId(?UID $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function __toString(): string
    {
        $output = "User(" . $this->getId()->getValue() . ", " . $this->getEmail() . ", " . $this->getlogin() . ",". $this->getCreatedAt()->format('Y-m-d H:i:s') . ")";
        return $output;
    }
}
