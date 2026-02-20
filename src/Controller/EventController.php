<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\EventFilterDTO;
use App\Service\EventService;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'app_event_list', methods: ['GET'])]
    #[OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1))]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20))]
    #[OA\Parameter(name: 'to', in: 'query', required: false, description: 'Filter events until this date (Y-m-d)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'location', in: 'query', required: false, description: 'Filter by location (partial match)', schema: new OA\Schema(type: 'string'))]
    public function list(Request $request, EventService $eventService, PaginationService $paginationService): Response
    {
        $pagination = $paginationService->paginate($request);
        $filter = EventFilterDTO::fromRequest($request);
        $events = $eventService->getPaginatedEvents($pagination['limit'], $pagination['offset'], $filter);
        $total = $eventService->countEvents($filter);

        return $this->json([
            'data' => $events,
            'pagination' => [
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'total' => $total,
            ],
        ], 200, [], ['groups' => ['public']]);
    }

    #[Route('/events/{id}', name: 'app_event_detail', methods: ['GET'])]
    public function detail(string $id, EventService $eventService): Response
    {
        $event = $eventService->getEventsByUUID($id);

        if (!$event) {
            return $this->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $event], 200, [], ['groups' => ['public']]);
    }
}
