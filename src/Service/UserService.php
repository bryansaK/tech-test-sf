<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UserCreateDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function createUser(UserCreateDTO $dto): User
    {
        if ($dto->email === '' || $dto->password === '') {
            throw new \InvalidArgumentException('Email and password are required');
        }

        $user = new User();
        $user->setEmail($dto->email);
        // Hash du mot de passe avec bcrypt
        $hashedPassword = password_hash($dto->password, PASSWORD_BCRYPT);
        if ($hashedPassword === false) {
            throw new \RuntimeException('Unable to hash password');
        }
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getUser(string $id): ?User
    {
        try {
            return $this->userRepository->find($id);
        } catch (\Exception) {
            return null;
        }
    }

    public function findByCredentials(string $email, string $password): ?User
    {
        if ($email === '' || $password === '') {
            return null;
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, (string) $user->getPassword())) {
            return null;
        }

        return $user;
    }
}
