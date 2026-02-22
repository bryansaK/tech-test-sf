<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CalendarService;
use App\Service\PaginationService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendarController extends AbstractController
{
    #[Route('/users/{userId}/calendar', name: 'user_calendar_list', methods: ['GET'])]
    #[OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1))]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20))]
    public function list(string $userId, Request $request, CalendarService $calendarService, PaginationService $paginationService): Response
    {
        $pagination = $paginationService->paginate($request);
        $events = $calendarService->getUserCalendarEvents($userId, $pagination['limit'], $pagination['offset']);

        return $this->json([
            'events' => $events,
            'pagination' => [
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'total' => count($events),
            ],
        ], Response::HTTP_OK, [], ['groups' => ['public']]);
    }

    #[Route('/users/{userId}/calendar', name: 'user_calendar_add_event', methods: ['POST'])]
    public function addEventToCalendar(string $userId, Request $request, CalendarService $calendarService): Response
    {
        $data = $request->toArray();
        $eventId = $data['eventId'] ?? null;

        if (!$eventId) {
            return $this->json(['error' => 'eventId is required'], Response::HTTP_BAD_REQUEST);
        }

        $calendar = $calendarService->addEventToUserCalendar($userId, (string) $eventId);

        if (!$calendar) {
            return $this->json(['error' => 'User or Event not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'message' => 'Event added to calendar',
            'calendarId' => $calendar->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/users/{userId}/calendar/{eventId}', name: 'user_calendar_remove_event', methods: ['DELETE'])]
    public function removeEventFromCalendar(string $userId, string $eventId, CalendarService $calendarService): Response
    {
        $removed = $calendarService->removeEventFromUserCalendar($userId, $eventId);

        if (!$removed) {
            return $this->json(['error' => 'Calendar entry not found'], Response::HTTP_NOT_FOUND);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
