<?php

namespace AlexanderA2\AdminBundle\EventSubscriber;

use AlexanderA2\AdminBundle\Event\MenuBuildEvent;
use AlexanderA2\AdminBundle\Helper\EntityHelper;
use AlexanderA2\AdminBundle\Helper\StringHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class AdminSidebarMenuBuildEventSubscriber implements EventSubscriberInterface
{
    protected const MENU_NAME = 'admin.sidebar_menu';

    public function __construct(
        protected EntityHelper $entityHelper,
        protected RouterInterface $router,
    ) {
    }

    public function onMenuBuild(MenuBuildEvent $event): void
    {
        if ($event->getName() !== self::MENU_NAME) {
            return;
        }
        $menu = $event->getMenu();
        $menu
            ->addChild('Home', ['route' => 'admin_home'])
            ->setExtra('icon', 'bi bi-grid-fill');

        foreach ($this->entityHelper->getEntityList() as $objectClassName) {
            $menu
                ->addChild($objectClassName)
                ->setLabel(StringHelper::getShortClassName($objectClassName))
                ->setUri($this->router->generate('admin_crud_index', [
                    'entityClassName' => $objectClassName,
                ]));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuBuildEvent::class => 'onMenuBuild',
        ];
    }
}
