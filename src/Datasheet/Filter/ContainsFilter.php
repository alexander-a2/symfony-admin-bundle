<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use AlexanderA2\AdminBundle\Datasheet\DataType\StringDataType;

class ContainsFilter extends AbstractFilter
{
    public const SHORT_NAME = 'has';

    protected array $attributes = [
        'value' => StringDataType::class,
    ];
}
