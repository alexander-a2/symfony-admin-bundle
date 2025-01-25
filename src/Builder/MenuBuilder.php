<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Event\MenuBuildEvent;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuBuilder
{
    protected const DEFAULT_MENU_ITEM_TYPE = 'secondary';

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function build(string $name): ItemInterface
    {
        $event = new MenuBuildEvent($name, (new MenuFactory())->createItem($name));
        $this->eventDispatcher->dispatch($event);

        return $event->getMenu();
    }

    public static function buildMenuItem(
        string  $label,
        string  $url = '',
        ?string $icon = null,
        string  $type = self::DEFAULT_MENU_ITEM_TYPE,
        ?array  $attributes = [],
        bool    $confirm = false,
    ): ItemInterface {
        if ($confirm) {
            $attributes['onClick'] = 'if(!confirm("Are you sure?")) return false;';
        }

        return (new MenuFactory())->createItem($label)
            ->setUri($url)
            ->setExtra('type', $type)
            ->setExtra('icon', $icon)
            ->setAttributes($attributes);
    }
}
