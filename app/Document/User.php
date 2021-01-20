<?php

declare(strict_types=1);

namespace App\Document;

class User
{
    public string $userId;
    public ?string $name;
    public array $output = [];

    public function __construct(string $userId, array $data)
    {
        $this->userId = $userId;
        $this->name = $data['name'] ?? null;
        $this->output = $data['output'] ?? [];
    }
}
