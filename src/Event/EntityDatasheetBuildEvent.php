<?php

namespace AlexanderA2\AdminBundle\Event;

use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;

class EntityDatasheetBuildEvent
{
    public function __construct(
        protected string $entityFqcn,
        protected DatasheetInterface $datasheet){
    }

    public function getEntityFqcn(): string
    {
        return $this->entityFqcn;
    }

    public function getDatasheet(): DatasheetInterface
    {
        return $this->datasheet;
    }
}
