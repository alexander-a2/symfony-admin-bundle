<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Component\EntitySettings;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class EntitySettingsBuilder
{
    public function __construct(
        protected RouterInterface $router,
        protected RequestStack $requestStack,
    ) {
    }

    public function build(EntitySettings $entitySettings)
    {
        if ($entitySettings->isSingleView()) {
            $this->buildForSingleView($entitySettings);
        } else {
            $this->buildForMultipleView($entitySettings);
        }
    }

    protected function buildForSingleView(EntitySettings $entitySettings): void
    {
        $currentRouteName = $this->requestStack->getCurrentRequest()->get('_route');

        /** Edit */
        if ($currentRouteName !== 'admin_crud_edit') {
            $entitySettings->getMenu()->addChild(
                MenuBuilder::buildMenuItem(
                    'Edit',
                    $this->router->generate('admin_crud_edit', [
                        'entityFqcn' => $entitySettings->getFqcn(),
                        'entityId' => $entitySettings->getId(),
                    ]),
                    'bi bi-pencil-square',
                )
            );
        }

        /** Delete */
        $entitySettings->getMenu()->addChild(
            MenuBuilder::buildMenuItem(
                'Delete',
                $this->router->generate('admin_crud_delete', [
                    'entityFqcn' => $entitySettings->getFqcn(),
                    'entityId' => $entitySettings->getId(),
                ]),
                'bi bi-trash',
                'danger',
                [],
                true,
            )
        );
    }

    protected function buildForMultipleView(EntitySettings $entitySettings)
    {
        $entitySettings->getMenu()->addChild(
            MenuBuilder::buildMenuItem(
                'New',
                $this->router->generate('admin_crud_edit', [
                    'entityFqcn' => $entitySettings->getFqcn(),
                ]),
                'bi bi-plus',
                'success',
            )
        );
    }
}
