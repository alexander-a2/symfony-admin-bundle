<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use AlexanderA2\AdminBundle\Datasheet\DataType\StringDataType;

class EqualsFilter extends AbstractFilter
{
    public const FULL_NAME = 'equals';

    public const SHORT_NAME = 'eq';

    protected array $attributes = [
        'value' => StringDataType::class,
    ];
}
