<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Event\MenuBuildEvent;
use AlexanderA2\AdminBundle\Helper\RouteHelper;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuBuilder
{
//    protected const DEFAULT_MENU_ITEM_TYPE = 'secondary';
//
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
//        protected RouteHelper              $routeHelper,
    ) {
    }
//
    public function build(string $name): ItemInterface
    {
        $event = new MenuBuildEvent($name, (new MenuFactory())->createItem($name));
        $this->eventDispatcher->dispatch($event);

        return $event->getMenu();
    }
//
//    public function addMenuItems(ItemInterface $menu, array $data, array $parametersData = []): void
//    {
//        foreach ($data as $item) {
//            $menuItem = self::buildMenuItem(
//                $item['label'],
//                isset($item['route']) ? $this->routeHelper->buildRoute(
//                    $item['route'],
//                    $item['routeParameters'] ?? [],
//                    $parametersData,
//                ) : '',
//                $item['type'] ?? 'primary',
//                $item['icon'] ?? '',
//                $item['attributes'] ?? [],
//                $item['hasConfirmation'] ?? false,
//            );
//
//            if ($item['children'] ?? []) {
//                $this->addMenuItems($menuItem, $item['children'], $parametersData);
//            }
//            $menu->addChild($menuItem);
//        }
//    }
//
//    public static function buildMenuItem(
//        string  $label,
//        string  $url = '',
//        string  $type = self::DEFAULT_MENU_ITEM_TYPE,
//        ?string $icon = null,
//        ?array  $attributes = [],
//        bool    $confirm = false,
//    ): ItemInterface {
//        if ($confirm) {
//            $attributes['onClick'] = 'if(!confirm("Are you sure?")) return false;';
//        }
//
//        return (new MenuFactory())->createItem($label)
//            ->setUri($url)
//            ->setExtra('type', $type)
//            ->setExtra('icon', $icon)
//            ->setAttributes($attributes);
//    }
}
