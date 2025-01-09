<?php

namespace AlexanderA2\AdminBundle\Datasheet\Builder\Column;

use AlexanderA2\AdminBundle\Datasheet\DataReader\ArrayDataReader;
use AlexanderA2\AdminBundle\Datasheet\DatasheetColumn;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use AlexanderA2\AdminBundle\Datasheet\DataType\ObjectDataType;
use AlexanderA2\AdminBundle\Datasheet\Registry\DataTypeRegistry;
use AlexanderA2\AdminBundle\Datasheet\Resolver\DataTypeResolver;

class ArrayDatasheetColumnBuilder implements ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getDataReader() instanceof ArrayDataReader;
    }

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface
    {
        $dataTypeResolver = new DataTypeResolver(
            (new DataTypeRegistry())->get(),
        );
        $firstRow = $datasheet->getSource()[0];

        foreach ($firstRow as $columnName => $sampleValue) {
            $dataType = empty($sampleValue) ? ObjectDataType::class : $dataTypeResolver->guess($sampleValue);
            $datasheet->addColumn(new DatasheetColumn($columnName, $dataType));
        }

        return $datasheet;
    }
}
