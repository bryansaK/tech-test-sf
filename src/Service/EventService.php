<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\EventDTO;
use App\DTO\EventFilterDTO;
use App\Entity\Event;
use App\Repository\EventRepository;

final class EventService
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    /**
     * @return EventDTO[]
     */
    public function getPaginatedEvents(int $limit, int $offset, EventFilterDTO $filter): array
    {
        $events = $this->eventRepository->findAllEventsByFilters($limit, $offset, $filter);

        $result = [];
        foreach ($events as $event) {
            $result[] = EventDTO::fromEntity($event);
        }

        return $result;
    }

    /**
     * @return EventDTO[]
     */
    public function getEventsByUUID(string $uuid): array
    {
        $event = $this->eventRepository->findBy(['uuid' => $uuid]);
        $result[] = EventDTO::fromEntity($event[0] ?? null);

        return $result;
    }

    public function countEvents(): int
    {
        return $this->eventRepository->count([]);
    }
}
