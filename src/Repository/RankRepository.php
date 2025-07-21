<?php

namespace App\Repository;

use App\Entity\Rank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rank>
 */
class RankRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rank::class);
    }

    /**
     * Получение всех званий в алфавитном порядке
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Поиск звания по точному названию
     */
    public function findByExactTitle(string $title): ?Rank
    {
        return $this->createQueryBuilder('r')
            ->where('r.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
