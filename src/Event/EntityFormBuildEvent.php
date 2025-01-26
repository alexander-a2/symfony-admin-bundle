<?php

namespace AlexanderA2\AdminBundle\Event;

use Symfony\Component\Form\FormBuilderInterface;

class EntityFormBuildEvent
{
    public function __construct(
        protected string $entityClassName,
        protected FormBuilderInterface $formBuilder,
    ) {
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function getFormBuilder(): FormBuilderInterface
    {
        return $this->formBuilder;
    }
}
