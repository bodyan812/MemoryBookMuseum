<?php

namespace App\Repository;

use App\Entity\Award;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Award>
 */
class AwardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Award::class);
    }

    /**
     * Поиск наград по названию (частичному совпадению)
     */
    public function findByTitlePartialMatch(string $title): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.title LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получение всех наград в алфавитном порядке
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
