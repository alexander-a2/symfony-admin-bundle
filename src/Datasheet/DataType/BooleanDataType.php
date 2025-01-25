<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

class BooleanDataType implements DataTypeInterface
{
    public static function toFormatted(mixed $value): string
    {
        if ($value) {
            return '<span class="badge bg-success m-2">Yes</span>';
        } else {
            return '<span class="badge bg-secondary m-2">No</span>';
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
