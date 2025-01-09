<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

use DateTime;
use DateTimeInterface;

class DateTimeDataType implements DataTypeInterface
{
    public static function toString($value): string
    {
        /** @var DateTime $value */
        return $value ? $value->format('Y-m-d h:i:s') : '';
    }

    public static function fromString($value): DateTime
    {
        return new DateTime($value);
    }

    public static function getFilters(): array
    {
        return [];
    }

    public static function is(mixed $value): bool
    {
        return $value instanceof DateTimeInterface;
    }
}
