<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

use AlexanderA2\AdminBundle\Datasheet\Filter\ContainsFilter;
use AlexanderA2\AdminBundle\Datasheet\Filter\EqualsFilter;

class StringDataType implements DataTypeInterface
{
    public const MAX_LENGTH = 20;

    public static function toFormatted(mixed $value): string
    {
        return '<span class="d-inline-block m-2">' . self::toString($value) . '</span>';
    }

    public static function toString($value): string
    {
        $value = (string)$value;

        if (mb_strlen($value) > self::MAX_LENGTH) {
            $value = mb_substr($value, 0, self::MAX_LENGTH) . '…';
        }

        return $value;
    }

    public static function fromString($value): string
    {
        return (string)$value;
    }

    public static function getFilters(): array
    {
        return [
            EqualsFilter::class,
            ContainsFilter::class,
        ];
    }

    public static function is(mixed $value): bool
    {
        return is_string($value);
    }
}
