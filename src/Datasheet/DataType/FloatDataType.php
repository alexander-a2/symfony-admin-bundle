<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

use AlexanderA2\AdminBundle\Datasheet\Filter\EqualsFilter;

class FloatDataType implements DataTypeInterface
{
    public static function toFormatted(mixed $value): string
    {
        return self::toString($value);
    }

    public static function toString($value): string
    {
        return (string) $value;
    }

    public static function fromString($value): float
    {
        return (float) $value;
    }

    public static function getFilters(): array
    {
        return [
            EqualsFilter::class,
        ];
    }

    public static function is(mixed $value): bool
    {
        return is_float($value);
    }
}
