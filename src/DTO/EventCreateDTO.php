<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;

final class EventCreateDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $date,
        public readonly ?string $location,
        public readonly ?string $imageUrl,
        public readonly ?string $category,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->toArray();

        return new self(
            title: (string) ($data['title'] ?? ''),
            description: $data['description'] ?? null,
            date: (string) ($data['date'] ?? ''),
            location: $data['location'] ?? null,
            imageUrl: $data['imageUrl'] ?? null,
            category: $data['category'] ?? null,
        );
    }
}
