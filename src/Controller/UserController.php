<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\UserCreateDTO;
use App\Service\UserService;
use App\Service\CalendarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request, UserService $userService): Response
    {
        $data = $request->toArray();
        $email = (string) ($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            return $this->json(['error' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userService->findByCredentials($email, $password);

        if (!$user) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ], Response::HTTP_OK);
    }

    #[Route('/register', name: 'user_create', methods: ['POST'])]
    public function create(Request $request, UserService $userService): Response
    {
        try {
            $dto = UserCreateDTO::fromRequest($request);
            $user = $userService->createUser($dto);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ], Response::HTTP_CREATED);
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
