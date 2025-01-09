<?php

namespace AlexanderA2\AdminBundle\Datasheet\Builder\Column;

use AlexanderA2\AdminBundle\Datasheet\DataReader\QueryBuilderDataReader;
use AlexanderA2\AdminBundle\Datasheet\DatasheetColumn;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use AlexanderA2\AdminBundle\Helper\EntityHelper;
use AlexanderA2\AdminBundle\Datasheet\Helper\QueryBuilderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDatasheetColumnBuilder implements ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getDataReader() instanceof QueryBuilderDataReader;
    }

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $datasheet->getDataReader()->getSource();
        $queryBuilder->getDQLPart('select');

        foreach ($queryBuilder->getDQLPart('select') as $select) {
            $select = QueryBuilderHelper::parseSelect($select);

            if (empty($select['fieldName'])) {
                if ($select['alias'] === QueryBuilderHelper::getPrimaryAlias($queryBuilder)) {
                    $this->addAllEntityFields(
                        $datasheet,
                        QueryBuilderHelper::getPrimaryClass($queryBuilder),
                        $queryBuilder->getEntityManager(),
                    );
                }
            }
        }

        return $datasheet;
    }

    protected function addAllEntityFields(
        DatasheetInterface     $datasheet,
        string                 $entityClassName,
        EntityManagerInterface $entityManager,
    ): void {
        foreach (EntityHelper::get($entityManager)->getEntityFields($entityClassName) as $fieldName => $fieldType) {
            $datasheet->addColumn(new DatasheetColumn($fieldName, EntityHelper::resolveDataTypeByFieldType($fieldType)));
        }
    }
}
