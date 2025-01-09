<?php

namespace AlexanderA2\AdminBundle\Event;

use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;

class EntityDatasheetBuildEvent
{
    public function __construct(
        protected string $entityClassName,
        protected DatasheetInterface $datasheet){
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function getDatasheet(): DatasheetInterface
    {
        return $this->datasheet;
    }
}
