<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * Поиск медиа по типу файла
     */
    public function findByFileType(string $fileType): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.fileType = :fileType')
            ->setParameter('fileType', $fileType)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получение медиа для конкретного ветерана
     */
    public function findByVeteran(int $veteranId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.veteran = :veteranId')
            ->setParameter('veteranId', $veteranId)
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
