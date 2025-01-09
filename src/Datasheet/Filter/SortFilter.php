<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use AlexanderA2\AdminBundle\Datasheet\DataType\IntegerDataType;

class SortFilter extends AbstractFilter
{
    public const SHORT_NAME = 'sort';

    protected array $attributes = [
        'by' => IntegerDataType::class,
        'direction' => IntegerDataType::class,
    ];
}
