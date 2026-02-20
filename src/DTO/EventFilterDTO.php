<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;

final class EventFilterDTO
{
    public function __construct(
        public readonly ?string $from = null,
        public readonly ?string $location = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            from: $request->query->get('from'),
            location: $request->query->get('location'),
        );
    }
}
