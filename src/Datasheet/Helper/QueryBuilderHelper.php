<?php

namespace AlexanderA2\AdminBundle\Datasheet\Helper;

use Doctrine\ORM\QueryBuilder;
use Exception;

class QueryBuilderHelper
{
    public static function getPrimaryAlias(QueryBuilder $queryBuilder): ?string
    {
        return $queryBuilder->getRootAliases()[0] ?? null;
    }

    public static function getPrimaryClass(QueryBuilder $queryBuilder): ?string
    {
        return $queryBuilder->getRootEntities()[0] ?? null;
    }

    public static function parseSelect(string $select): array
    {
        $pattern = '/^(\w+)(?:\.(\w+))?(?: AS (\w+))?$/';

        if (!preg_match($pattern, $select, $matches)) {
            throw new Exception('Parse failed: ' . $select);
        }

        return [
            'alias' => $matches[1],
            'fieldName' => $matches[2] ?? null,
            'as' => $matches[3] ?? null,
        ];
    }
}
