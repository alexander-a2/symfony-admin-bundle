<?php

namespace AlexanderA2\AdminBundle\Datasheet\Registry;

use AlexanderA2\AdminBundle\Datasheet\DataType\BooleanDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\DateDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\DateTimeDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\FloatDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\IntegerDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\ObjectDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\StringDataType;

class DataTypeRegistry
{
    public function get(): array
    {
        return [
            BooleanDataType::class,
            DateDataType::class,
            DateTimeDataType::class,
            FloatDataType::class,
            IntegerDataType::class,
            ObjectDataType::class,
            StringDataType::class,
        ];
    }
}
