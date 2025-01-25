<?php

namespace AlexanderA2\AdminBundle\EventSubscriber;

use AlexanderA2\AdminBundle\Builder\SidebarMenuBuilder;
use AlexanderA2\AdminBundle\Event\MenuBuildEvent;
use AlexanderA2\AdminBundle\Helper\EntityHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class AdminSidebarMenuBuildEventSubscriber implements EventSubscriberInterface
{
    protected const MENU_NAME = 'admin.sidebar_menu';

    public function __construct(
        protected EntityHelper $entityHelper,
        protected RouterInterface $router,
        protected SidebarMenuBuilder $sidebarMenuBuilder,
    ) {
    }

    public function onMenuBuild(MenuBuildEvent $event): void
    {
        if ($event->getName() !== self::MENU_NAME) {
            return;
        }
        $this->sidebarMenuBuilder->addMenuItems($event->getMenu());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuBuildEvent::class => 'onMenuBuild',
        ];
    }
}
