<?php

namespace App\Repository;

use App\Entity\Calendar;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calendar>
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    /**
     * @return Calendar[]
     */
    public function findUserEvents(User $user, int $limit, int $offset): array
    {
        return $this->findBy(['user' => $user], null, $limit, $offset);
    }
}
