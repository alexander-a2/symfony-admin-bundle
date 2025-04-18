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

    public function onMenuBuildEarly(MenuBuildEvent $event): void
    {
        if ($event->getName() !== self::MENU_NAME) {
            return;
        }
        $event->getMenu()
            ->addChild('Home', ['route' => 'admin_home'])
            ->setExtra('icon', 'bi bi-house-fill');
    }

    public function onMenuBuildLate(MenuBuildEvent $event): void
    {
        if ($event->getName() !== self::MENU_NAME) {
            return;
        }
        $this->sidebarMenuBuilder->addEntitiesListToMenu($event->getMenu());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuBuildEvent::class => [
                ['onMenuBuildEarly', 700],
                ['onMenuBuildLate', -700],
            ],
        ];
    }
}
