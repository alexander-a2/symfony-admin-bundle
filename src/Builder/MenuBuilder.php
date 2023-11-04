<?php
declare(strict_types=1);

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\SymfonyAdminBundle\Event\MenuBuildEvent;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuBuilder
{
    private const HOME_PAGE_ITEM_NAME = 'homepage';
    private const ADMIN_PAGE_ITEM_NAME = 'admin_panel';

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected RouterInterface          $router,
        protected TranslatorInterface      $translator,
    ) {
    }

    public function build(string $name): ItemInterface
    {
        $menu = (new MenuFactory())->createItem($name);

        $menu->addChild(self::HOME_PAGE_ITEM_NAME)
            ->setLabel($this->translator->trans('admin.main_menu.' . self::HOME_PAGE_ITEM_NAME))
            ->setUri('/');
        $menu->addChild(self::ADMIN_PAGE_ITEM_NAME)
            ->setLabel($this->translator->trans('admin.main_menu.' . self::ADMIN_PAGE_ITEM_NAME))
            ->setUri($this->router->generate('admin_index'));

        $event = new MenuBuildEvent($menu);
        $this->eventDispatcher->dispatch($event);

        return $event->getMenu();
    }
}