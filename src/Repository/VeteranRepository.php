<?php

namespace App\Repository;

use App\Entity\Veteran;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Veteran>
 */
class VeteranRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Veteran::class);
    }

    /**
     * Поиск ветеранов по типу войны
     */
    public function findByWarType(string $warType): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.warType = :warType')
            ->setParameter('warType', $warType)
            ->orderBy('v.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v');

        // Обрабатываем как одиночные значения, а не массивы
        if (isset($filters['warType'])) {
            $qb->andWhere('v.warType = :warType')
                ->setParameter('warType', $filters['warType']);
        }

        if (isset($filters['rank.id'])) {
            $qb->andWhere('v.rank = :rankId')
                ->setParameter('rankId', $filters['rank.id']);
        }

        if (isset($filters['awards.id'])) {
            $qb->join('v.awards', 'a')
                ->andWhere('a.id = :awardId')
                ->setParameter('awardId', $filters['awards.id']);
        }

        // Оставляем другие фильтры
        if (isset($filters['birthDate'])) {
            $qb->andWhere('YEAR(v.birthDate) = :birthYear')
                ->setParameter('birthYear', $filters['birthDate']);
        }

        if (isset($filters['deathDate'])) {
            $qb->andWhere('YEAR(v.deathDate) = :deathYear')
                ->setParameter('deathYear', $filters['deathDate']);
        }

        return $qb
            ->orderBy('v.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
    /**
     * Поиск ветеранов по ФИО (частичное совпадение)
     */
    public function searchByName(string $name): array
    {
        return $this->createQueryBuilder('v')
            ->where('CONCAT(v.lastName, \' \', v.firstName, \' \', COALESCE(v.middleName, \'\')) LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->orderBy('v.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получение ветеранов с наградами (жадная загрузка)
     */
    public function findAllWithAwards(): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.awards', 'a')
            ->addSelect('a')
            ->orderBy('v.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получение ветеранов, рожденных в определенный период
     */
    public function findByBirthDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.birthDate BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('v.birthDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
