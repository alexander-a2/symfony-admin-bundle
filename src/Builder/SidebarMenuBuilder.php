<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Helper\EntityHelper;
use AlexanderA2\AdminBundle\Helper\StringHelper;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\RouterInterface;

class SidebarMenuBuilder
{
    public const string DATABASE_SECTION_NAME = 'Database';

    public function __construct(
        protected EntityHelper $entityHelper,
        protected RouterInterface $router,
    ) {
    }

    /** Strategy 1 */
    public function addEntitiesListToMenu(ItemInterface $menu): void
    {
        $parent = $menu
            ->addChild(self::DATABASE_SECTION_NAME)
            ->setLabel(self::DATABASE_SECTION_NAME)
            ->setExtra('icon', 'bi bi-grid-fill');

        foreach ($this->entityHelper->getEntityList() as $objectClassName) {
            $parent
                ->addChild(StringHelper::toSnakeCase($objectClassName))
                ->setLabel(StringHelper::getShortClassName($objectClassName))
                ->setUri($this->router->generate('admin_crud_list', ['entityFqcn' => $objectClassName]));
        }
    }
}
