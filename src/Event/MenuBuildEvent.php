<?php

namespace AlexanderA2\SymfonyAdminBundle\Event;

use Knp\Menu\ItemInterface;

class MenuBuildEvent
{
    public function __construct(
        protected ItemInterface $menu
    ) {
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}