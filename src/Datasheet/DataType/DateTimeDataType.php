<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

use DateTime;
use DateTimeInterface;

class DateTimeDataType implements DataTypeInterface
{
    const TIME_FORMAT = 'H:i';
    const DATE_FORMAT = 'd M, Y';
    const DATE_TIME_FORMAT = 'H:i, d M, Y';

    public static function toFormatted(mixed $value): string
    {
        return '<div class="d-inline-block border rounded-3 m-0 bg-light my-1 mx-1 text-center" style="padding:3px 7px;width:100px;"><small>'
            . $value->format(self::DATE_FORMAT) . '</small></div> '
            . '<div class="d-inline-block border rounded-3 m-0 bg-light my-1 text-center" style="padding:3px 7px;width:50px;"><small>'
            . $value->format(self::TIME_FORMAT) . '</small></div> ';
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
