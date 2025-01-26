<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

class BooleanDataType implements DataTypeInterface
{
    public static function toFormatted(mixed $value): string
    {
        if ($value) {
            return '<div class="d-inline-block bg-success text-white border rounded-3 m-2 p-0 px-3 text-center"><small>Yes</small></div>';
        } else {
            return '<div class="d-inline-block bg-light text-secondary border rounded-3 m-2 p-0 px-3 text-center"><small>No</small></div>';
        }
    }

    public static function toString($value): string
    {
        return (string) $value;
    }

    public static function fromString($value): bool
    {
        return (bool) $value;
    }

    public static function getFilters(): array
    {
        return [];
    }

    public static function is(mixed $value): bool
    {
        return is_bool($value);
    }
}
