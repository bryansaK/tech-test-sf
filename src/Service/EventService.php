<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\EventDTO;
use App\DTO\EventCreateDTO;
use App\DTO\EventFilterDTO;
use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class EventService
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $entityManager,
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

    public function createEvent(EventCreateDTO $dto): EventDTO
    {
        if ($dto->title === '' || $dto->date === '') {
            throw new \InvalidArgumentException('Title and date are required');
        }

        try {
            $date = new \DateTimeImmutable($dto->date);
        } catch (\Exception) {
            throw new \InvalidArgumentException('Invalid date format');
        }

        $event = new Event();
        $event->setTitle($dto->title);
        $event->setDescription($dto->description);
        $event->setDate($date);
        $event->setLocation($dto->location);
        $event->setImageUrl($dto->imageUrl);
        $event->setCategory($dto->category ?? 'culture');

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return EventDTO::fromEntity($event);
    }

    public function deleteEvent(string $id): bool
    {
        try {
            $event = $this->eventRepository->find($id);
        } catch (\Exception) {
            return false;
        }

        if (!$event) {
            return false;
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return true;
    }

}
