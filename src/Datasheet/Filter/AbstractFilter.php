<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractFilter implements FilterInterface
{
    public const FULL_NAME = __CLASS__;

    public const SHORT_NAME = __CLASS__;

    public function getShortName(): string
    {
        return static::SHORT_NAME;
    }

    public function getFullName(): string
    {
        return static::FULL_NAME;
    }

    public function getDefaultParameters(): array
    {
        return [];
    }

    public function addForm(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('filter', FormType::class);
    }
}
