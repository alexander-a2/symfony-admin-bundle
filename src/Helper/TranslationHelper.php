<?php

namespace AlexanderA2\SymfonyAdminBundle\Helper;

use AlexanderA2\PhpDatasheet\Helper\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationHelper
{
    public function __construct(
        protected TranslatorInterface $translator,
    ) {
    }

    public function translate(string $originalString): string
    {
        $translated = $this->translator->trans($originalString);

        if ($translated != $originalString) {
            return $translated;
        }
        $hasPath = str_contains($originalString, '.');

        if (!$hasPath) {
            return StringHelper::toReadable($originalString);
        }
        $tmp = explode('.', $originalString);

        return StringHelper::toReadable($tmp[count($tmp) - 1]);
    }
}