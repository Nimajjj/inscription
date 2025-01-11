<?php

namespace App\VO;

use Random\RandomException;

final class UID
{
    private string $value;

    /**
     * @throws RandomException
     */
    public function __construct(?string $value = null)
    {
        $this->value = $value ?: $this->generateUID();
    }

    public function __tostring(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @throws RandomException
     */
    private function generateUID(): string
    {
        // Generate a UUID (version 4) that is valid and unique
        return bin2hex(random_bytes(16)); // 32 characters long UID
    }
}