<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class BirthYearFilter extends AbstractFilter
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        LoggerInterface $logger = null,
        array $properties = null,
        NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    protected function filterProperty(
        string $property,
               $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if ($property !== 'birthDate' || !$value) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $paramName = $queryNameGenerator->generateParameterName('birthYear');

        $queryBuilder
            ->andWhere("YEAR($alias.birthDate) >= :$paramName")
            ->setParameter($paramName, $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'birthDate' => [
                'property' => 'birthDate',
                'type' => 'integer',
                'required' => false,
                'description' => 'Год рождения от (YYYY)',
                'openapi' => [
                    'example' => 1905,
                    'default' => 1905
                ]
            ]
        ];
    }
}
