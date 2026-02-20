<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Event;
use Symfony\Component\Serializer\Attribute\Groups;

final class EventDTO
{
    public function __construct(
        #[Groups(['public'])]
        public readonly string $id,
        #[Groups(['public'])]
        public readonly string $title,
        #[Groups(['public'])]
        public readonly ?string $description,
        #[Groups(['public'])]
        public readonly string $date,
        #[Groups(['public'])]
        public readonly ?string $location,
        #[Groups(['public'])]
        public readonly ?string $imageUrl,
    ) {}

    public static function fromEntity(Event $event): self
    {
        return new self(
            id: $event->getId(),
            title: $event->getTitle(),
            description: $event->getDescription(),
            date: $event->getDate()->format('Y-m-d\TH:i:s.v\Z'),
            location: $event->getLocation(),
            imageUrl: $event->getImageUrl(),
        );
    }
}
