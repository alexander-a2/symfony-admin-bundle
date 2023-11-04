<?php

namespace AlexanderA2\SymfonyAdminBundle\EventSubscriber;

use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\StringHelper;
use AlexanderA2\SymfonyAdminBundle\AdminBundle;
use AlexanderA2\SymfonyAdminBundle\Event\MenuBuildEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class AdminPanelMainMenuBuildSubscriber implements EventSubscriberInterface
{
    private const MENU_ENTITY_LIST_GROUP_TITLE = 'database';

    public function __construct(
        protected RouterInterface        $router,
        protected EntityManagerInterface $entityManager,
    ) {
    }

    public function addEntityItems(MenuBuildEvent $event): void
    {
        if ($event->getMenu()->getName() !== AdminBundle::MAIN_MENU_NAME) {
            return;
        }

        $event->getMenu()->addChild(self::MENU_ENTITY_LIST_GROUP_TITLE);

        foreach (EntityHelper::getEntityList($this->entityManager) as $objectClassName) {
            $menuItem = $event->getMenu()
                ->getChild(self::MENU_ENTITY_LIST_GROUP_TITLE)
                ->addChild($objectClassName);
            $menuItem
                ->setLabel(StringHelper::toReadable(StringHelper::getShortClassName($objectClassName)))
                ->setUri($this->router->generate('admin_crud_index', [
                    'entityClassName' => $objectClassName,
                ]));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuBuildEvent::class =>
                ['addEntityItems', -400],
        ];
    }
}