<?php

namespace AlexanderA2\AdminBundle\Twig;

use AlexanderA2\AdminBundle\Helper\StringHelper;
use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringHelperExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('toReadable', [$this, 'toReadable']),
            new TwigFilter('readableTitle', [$this, 'getReadableTitle']),
            new TwigFilter('urlize', [$this, 'urlize']),
            new TwigFilter('toCamelCase', [$this, 'toCamelCase']),
            new TwigFilter('toSnakeCase', [$this, 'toSnakeCase']),
            new TwigFilter('toPascalCase', [$this, 'toPascalCase']),
            new TwigFilter('removeEmoji', [$this, 'removeEmoji']),
            new TwigFilter('formatDateSimple', [$this, 'formatDateSimple']),
            new TwigFilter('className', [$this, 'getClassName']),
            new TwigFilter('shortClassName', [$this, 'getShortClassName']),
        ];
    }

    public function toReadable($input): string
    {
        return StringHelper::toReadable($input);
    }

    public function urlize($input): string
    {
        return StringHelper::urlize($input);
    }

    public function toCamelCase($input): string
    {
        return StringHelper::toCamelCase($input);
    }

    public function toSnakeCase($input): string
    {
        return StringHelper::toSnakeCase($input);
    }

    public function toPascalCase($input): string
    {
        return StringHelper::toPascalCase($input);
    }

    public function removeEmoji($text): string
    {
        return StringHelper::removeEmoji($text);
    }

    public function formatDateSimple(DateTimeInterface $datetime, $format, $hoursShift = 0): string
    {
        return $datetime->modify('+' . $hoursShift . ' hours')->format($format);
    }

    public function translate($originalString): string
    {
        return $this->translationHelper->translate($originalString);
    }

    public function getClassName($object): string
    {
        return get_class($object);
    }

    public function getShortClassName($object): string
    {
        return StringHelper::getShortClassName($object);
    }
}
