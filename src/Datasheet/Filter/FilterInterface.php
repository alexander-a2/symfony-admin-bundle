<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use Symfony\Component\Form\FormBuilderInterface;

interface FilterInterface
{
    public function getShortName(): string;

    public function getFullName(): string;

    public function getDefaultParameters(): array;

    public function addForm(FormBuilderInterface $formBuilder): void;
}
