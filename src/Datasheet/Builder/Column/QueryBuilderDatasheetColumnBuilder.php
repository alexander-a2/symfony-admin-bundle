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
        DatasheetInterface $datasheet,
        string $entityFqcn,
        EntityManagerInterface $entityManager,
    ): void {
        foreach (EntityHelper::get($entityManager)->getEntityFields($entityFqcn) as $fieldName => $fieldType) {
            $column = new DatasheetColumn($fieldName, EntityHelper::resolveDataTypeByFieldType($fieldType));
            $this->setFieldSettings($column, $fieldType);
            $datasheet->addColumn($column);
        }
    }

    public function setFieldSettings(DatasheetColumn $column, string $fieldType): void
    {
        if ($column->getName() === 'id') {
            $column->setWidth(50);
            $column->setAlign('right');
            return;
        }

        switch ($fieldType) {
            case 'integer':
            case 'float':
                $column->setAlign('right');
                $column->setWidth(100);
                break;
            case 'date':
                $column->setWidth(140);
                break;
            case 'datetime':
                $column->setWidth(190);
                break;
            case 'boolean':
                $column->setAlign('center');
                $column->setWidth(100);
                break;
        }
    }
}
