<?php

namespace AlexanderA2\AdminBundle\Event;

use AlexanderA2\AdminBundle\Component\EntitySettings;

class EntitySettingsBuildEvent
{
    public function __construct(
        protected EntitySettings $entitySettings,
    ){
    }

    public function getEntitySettings(): EntitySettings
    {
        return $this->entitySettings;
    }
}
