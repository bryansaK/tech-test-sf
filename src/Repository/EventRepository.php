<?php

namespace App\Repository;

use App\DTO\EventFilterDTO;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    //fonction custiom pour la paganiation et les filters 
    public function findAllEventsByFilters(int $limit, int $offset, EventFilterDTO $filters): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($filters->location !== null) {
            $qb->andWhere('e.location LIKE :location')
               ->setParameter('location', '%' . $filters->location . '%');
        }

        if ($filters->from !== null) {
            $qb->andWhere('e.date >= :dateFrom')
               ->setParameter('dateFrom', new \DateTime($filters->from));
        }

        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
