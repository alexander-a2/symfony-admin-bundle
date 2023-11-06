<?php
declare(strict_types=1);

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\SymfonyAdminBundle\Event\MenuBuildEvent;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuBuilder
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function build(string $name): ItemInterface
    {
        $event = new MenuBuildEvent((new MenuFactory())->createItem($name));
        $this->eventDispatcher->dispatch($event);

        return $event->getMenu();
    }
}