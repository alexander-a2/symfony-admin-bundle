<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

use DateTime;

class DateDataType implements DataTypeInterface
{
    const DATE_FORMAT = 'd M, Y';
    const DATE_DATEFORMAT = 'd M, Y';

    public static function toFormatted(mixed $value): string
    {
        return '<div class="border rounded-3 bg-light m-1 text-center" style="padding:3px 7px;width:100px;">'
            .'<small>' . $value->format(self::DATE_DATEFORMAT) . '</small></div>';
    }

    public static function toString($value): string
    {
        /** @var DateTime $value */
        return $value ? $value->format(self::DATE_FORMAT) : '';
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
        return false;
    }
}
