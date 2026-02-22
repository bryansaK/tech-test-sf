<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\EventDTO;
use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CalendarService
{
    public function __construct(
        private readonly CalendarRepository $calendarRepository,
        private readonly UserRepository $userRepository,
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function addEventToUserCalendar(string $userId, string $eventId): ?Calendar
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return null;
        }

        $event = $this->eventRepository->find($eventId);
        if (!$event) {
            return null;
        }

        $existing = $this->calendarRepository->findOneBy(['user' => $user, 'event' => $event]);
        if ($existing) {
            return $existing;
        }

        $calendar = new Calendar();
        $calendar->setUser($user);
        $calendar->setEvent($event);

        $this->entityManager->persist($calendar);
        $this->entityManager->flush();

        return $calendar;
    }

    public function removeEventFromUserCalendar(string $userId, string $eventId): bool
    {
        $user = $this->userRepository->find($userId);
        $event = $this->eventRepository->find($eventId);

        if (!$user || !$event) {
            return false;
        }

        $calendar = $this->calendarRepository->findOneBy(['user' => $user, 'event' => $event]);
        if (!$calendar) {
            return false;
        }

        $this->entityManager->remove($calendar);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @return EventDTO[]
     */
    public function getUserCalendarEvents(string $userId, int $limit, int $offset): array
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return [];
        }

        $calendars = $this->calendarRepository->findUserEvents($user, $limit, $offset);

        $result = [];
        foreach ($calendars as $calendar) {
            $event = $calendar->getEvent();
            if ($event !== null) {
                $result[] = EventDTO::fromEntity($event);
            }
        }

        return $result;
    }
}
