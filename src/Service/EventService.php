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

    public function getEventsByUUID(string $id): ?EventDTO
    {
        try {
            $event = $this->eventRepository->find($id);
        } catch (\Exception) {
            return null;
        }

        if (!$event) {
            return null;
        }

        return EventDTO::fromEntity($event);
    }

}
