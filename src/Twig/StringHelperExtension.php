<?php

namespace AlexanderA2\SymfonyAdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringHelperExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('translate', [$this, 'translate']),
        ];
    }

    public function translate(string $string): string
    {
        return $string;
    }
}