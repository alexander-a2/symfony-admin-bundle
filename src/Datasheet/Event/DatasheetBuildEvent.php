<?php

namespace AlexanderA2\AdminBundle\Datasheet\Event;

use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;

class DatasheetBuildEvent
{
    public function __construct(
        protected DatasheetInterface $datasheet
    ) {
    }

    public function getDatasheet(): DatasheetInterface
    {
        return $this->datasheet;
    }
}
