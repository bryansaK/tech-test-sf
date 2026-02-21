<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;

final class UserCreateDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->toArray();

        return new self(
            email: (string) ($data['email'] ?? ''),
            password: (string) ($data['password'] ?? ''),
        );
    }
}
