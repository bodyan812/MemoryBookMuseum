<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class VeteranFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
               $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $alias = $queryBuilder->getRootAliases()[0];
        $paramName = $queryNameGenerator->generateParameterName($property);

        switch ($property) {
            case 'warType':
                $queryBuilder
                    ->andWhere("$alias.warType = :$paramName")
                    ->setParameter($paramName, $value);
                break;

            case 'ranks':
                $queryBuilder
                    ->andWhere("$alias.rank = :$paramName")
                    ->setParameter($paramName, $value);
                break;

            case 'awards':
                $queryBuilder
                    ->join("$alias.awards", "a")
                    ->andWhere("a.id = :$paramName")
                    ->setParameter($paramName, $value);
                break;

            case 'birthDate':
                // Фильтр по году рождения: от 1 января указанного года
                $startDate = new \DateTime(($value ?: 1905) . '-01-01');
                $queryBuilder
                    ->andWhere("$alias.birthDate >= :{$paramName}_start")
                    ->setParameter("{$paramName}_start", $startDate);
                break;

            case 'deathDate':
                // Фильтр по году смерти: до 31 декабря указанного года
                $endDate = new \DateTime(($value ?: 1945) . '-12-31');
                $queryBuilder
                    ->andWhere("$alias.deathDate <= :{$paramName}_end")
                    ->setParameter("{$paramName}_end", $endDate);
                break;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'warType' => [
                'property' => 'warType',
                'type' => 'string',
                'required' => false,
                'description' => 'Тип конфликта',
                'openapi' => [
                    'enum' => ['afghan', 'ww2', 'svo', 'chechen', 'local'],
                    'example' => 'ww2'
                ]
            ],
            'ranks' => [
                'property' => 'ranks',
                'type' => 'int',
                'required' => false,
                'description' => 'ID звания',
                'openapi' => [
                    'example' => 1
                ]
            ],
            'awards' => [
                'property' => 'awards',
                'type' => 'int',
                'required' => false,
                'description' => 'ID награды',
                'openapi' => [
                    'example' => 5
                ]
            ],
            'birthDate' => [
                'property' => 'birthDate',
                'type' => 'int',
                'required' => false,
                'description' => 'Год рождения от (YYYY)',
                'openapi' => [
                    'default' => 1905,
                    'example' => 1920
                ]
            ],
            'deathDate' => [
                'property' => 'deathDate',
                'type' => 'int',
                'required' => false,
                'description' => 'Год смерти до (YYYY)',
                'openapi' => [
                    'default' => 1945,
                    'example' => 1943
                ]
            ]
        ];
    }
}
