<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataType;

use Exception;

class ObjectsDataType implements DataTypeInterface
{
    private const LIMIT = 20;

    public static function toFormatted(mixed $value): string
    {
        return '<div class="m-2">' . self::toString($value) . '</div>';
    }

    public static function toString($value): string
    {
        if (is_iterable($value)) {
            $stringParts = [];

            foreach($value as $item){
                $stringParts[] = ObjectDataType::toString($item);
            }

            return implode(', ', $stringParts);
        }

        return 'object';
    }

    public static function fromString($value): string
    {
        throw new Exception();
    }

    public static function prepare(string $value): string
    {
        return mb_substr($value, 0, self::LIMIT);
    }

    public static function getFilters(): array
    {
        return [];
    }

    public static function is(mixed $value): bool
    {
        return is_object($value);
    }
}
