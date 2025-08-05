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
    public function findByFilters(
        ?string $warType = null,
        ?int $rankId = null,
        ?int $awardId = null,
        ?int $birthYear = null,
        ?int $deathYear = null,
        ?string $searchQuery = null
    ): array {
        $qb = $this->createQueryBuilder('v');

        if ($warType) {
            $qb->andWhere('v.warType = :warType')
                ->setParameter('warType', $warType);
        }

        if ($rankId) {
            $qb->andWhere('v.rank = :rankId')
                ->setParameter('rankId', $rankId);
        }

        if ($awardId) {
            $qb->join('v.awards', 'a')
                ->andWhere('a.id = :awardId')
                ->setParameter('awardId', $awardId);
        }

        if ($birthYear) {
            $startDate = new \DateTime($birthYear . '-01-01');
            $qb->andWhere('v.birthDate >= :birthDate')
                ->setParameter('birthDate', $startDate);
        }

        if ($deathYear) {
            $endDate = new \DateTime($deathYear . '-12-31');
            $qb->andWhere('v.deathDate <= :deathDate')
                ->setParameter('deathDate', $endDate);
        }

        if ($searchQuery) {
            $qb->andWhere('CONCAT(v.lastName, \' \', v.firstName, \' \', COALESCE(v.middleName, \'\')) LIKE :searchQuery')
                ->setParameter('searchQuery', '%'.$searchQuery.'%');
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
