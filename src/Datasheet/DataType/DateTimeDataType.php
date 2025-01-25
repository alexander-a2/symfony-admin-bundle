<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

use DateTime;
use DateTimeInterface;

class DateTimeDataType implements DataTypeInterface
{
    const TIME_FORMAT = 'H:i';
    const DATE_FORMAT = 'd F, Y';
    const DATE_TIME_FORMAT = 'H:i, d F, Y';

    public static function toFormatted(mixed $value): string
    {
        return '<div class="border rounded-3 m-0 bg-light d-flex m-1" style="padding:3px 7px;max-width:190px;">'
            . '<span class="flex-shrink-0 col-3"><small>' . $value->format(self::TIME_FORMAT) . '</small></span> '
            . '<span class="flex-grow-1"><small>' . $value->format(self::DATE_FORMAT) . '</small></span> ';
    }

    public static function toString($value): string
    {
        /** @var DateTime $value */
        return $value ? $value->format(self::DATE_TIME_FORMAT) : '';
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
