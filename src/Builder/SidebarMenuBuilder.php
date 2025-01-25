<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Helper\EntityHelper;
use AlexanderA2\AdminBundle\Helper\StringHelper;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\RouterInterface;

class SidebarMenuBuilder
{
    public function __construct(
        protected EntityHelper $entityHelper,
        protected RouterInterface $router,
    ) {
    }

    public function addMenuItems(ItemInterface $menu): void
    {
        $menu
            ->addChild('Home', ['route' => 'admin_home'])
            ->setExtra('icon', 'bi bi-house-fill');

        // todo: multiple strategies
        $this->addEntitiesListToMenu($menu);
    }

    /** Strategy 1 */
    protected function addEntitiesListToMenu(ItemInterface $menu): void
    {
        $parent = $menu
            ->addChild('Database')
            ->setLabel('Database')
            ->setExtra('icon', 'bi bi-grid-fill');

        foreach ($this->entityHelper->getEntityList() as $objectClassName) {
            $parent
                ->addChild(StringHelper::toSnakeCase($objectClassName))
                ->setLabel(StringHelper::getShortClassName($objectClassName))
                ->setUri($this->router->generate('admin_crud_list', ['entityFqcn' => $objectClassName]));
        }
    }
}
