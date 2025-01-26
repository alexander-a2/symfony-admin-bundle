<?php

namespace AlexanderA2\AdminBundle\EventSubscriber;

use AlexanderA2\AdminBundle\Builder\EntitySettingsBuilder;
use AlexanderA2\AdminBundle\Event\EntitySettingsBuildEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntitySettingsBuildSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EntitySettingsBuilder $entitySettingsBuilder,
    ) {
    }

    public function onEntitySettingsBuild(EntitySettingsBuildEvent $event): void
    {
        $this->entitySettingsBuilder->build($event->getEntitySettings());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntitySettingsBuildEvent::class => [
                ['onEntitySettingsBuild', 900],
            ],
        ];
    }
}
