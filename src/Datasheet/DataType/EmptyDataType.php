<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

class EmptyDataType implements DataTypeInterface
{
    public static function toFormatted(mixed $value): string
    {
        return '<span style="color:#cccccc;">Null</span>';
    }

    public static function toString($value): string
    {
        return 'empty';
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
        return is_null($value);
    }
}
