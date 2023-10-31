<?php
declare(strict_types=1);

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\SymfonyAdminBundle\Event\MenuBuildEvent;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuBuilder
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected RouterInterface          $router,
    ) {
    }

    public function build(string $name): ItemInterface
    {
        $factory = new MenuFactory();
        $menu = $factory->createItem($name);
        $menu->addChild('Home', ['uri' => $this->router->generate('admin_index')]);
        $event = new MenuBuildEvent($menu);
        $this->eventDispatcher->dispatch($event);

        return $event->getMenu();
    }
}