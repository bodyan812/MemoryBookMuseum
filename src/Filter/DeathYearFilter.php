<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class DeathYearFilter extends AbstractFilter
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
        if ($property !== 'deathDate' || !$value) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $paramName = $queryNameGenerator->generateParameterName('deathYear');

        $queryBuilder
            ->andWhere("YEAR($alias.deathDate) <= :$paramName")
            ->setParameter($paramName, $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'deathDate' => [
                'property' => 'deathDate',
                'type' => 'integer',
                'required' => false,
                'description' => 'Год смерти до (YYYY)',
                'openapi' => [
                    'example' => 1945,
                    'default' => 1945
                ]
            ]
        ];
    }
}
