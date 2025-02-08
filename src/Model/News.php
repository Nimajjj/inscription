<?php

namespace App\Model;
use App\VO\Uid;

final class News implements DataModel
{
    private ?Uid $id;
    private string $content;
    private \DateTimeImmutable $created_at;

    public function getId(): Uid
    {
        return $this->id;
    }

    public function setId(?Uid $id): News
    {
        $this->id = $id;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): News
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): News
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function __tostring(): string
    {
        $output = "News(" . $this->getId()->getValue() . ", " . $this->getContent() . ", " . $this->getCreatedAt()->format('Y-m-d H:i:s') . ")";
        return $output;
    }


}