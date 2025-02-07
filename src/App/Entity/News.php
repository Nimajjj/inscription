<?php

namespace App\App\Entity;

use App\VO\Uid;
use DateTimeImmutable;

class News
{
    private Uid $id;
    private string $content;
    private DateTimeImmutable $createdAt;

    public function __construct(Uid $id, string $content, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uid
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return "News ID: {$this->id}, Content: {$this->content}, Created At: {$this->createdAt->format('Y-m-d H:i:s')}";
    }
}