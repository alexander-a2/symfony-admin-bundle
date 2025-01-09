<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Datasheet\Datasheet;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use AlexanderA2\AdminBundle\Event\EntityDatasheetBuildEvent;
use AlexanderA2\AdminBundle\Helper\EntityHelper;
use AlexanderA2\AdminBundle\Helper\ObjectHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EntityDatasheetBuilder
{
    public function __construct(
        protected EntityManagerInterface   $entityManager,
        protected RouterInterface          $router,
        protected EntityHelper             $entityHelper,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function build(
        string $entityClassName,
    ): DatasheetInterface {
        $datasheet = new Datasheet($this->entityManager->getRepository($entityClassName));
        $this->addLinkToPrimaryField($datasheet, $entityClassName, $this->router);

        foreach ($this->entityHelper->getEntityFields($entityClassName) as $fieldName => $fieldType) {
            if (in_array($fieldType, EntityHelper::RELATION_FIELD_TYPES)) {
                $this->addLinkToRelationField($datasheet, $entityClassName, $fieldName, $fieldType, $this->router);
            }
        }
        $this->eventDispatcher->dispatch(new EntityDatasheetBuildEvent($entityClassName, $datasheet));

        return $datasheet;
    }

    protected function addLinkToPrimaryField(
        DatasheetInterface $datasheet,
        string             $entityClassName,
        RouterInterface    $router
    ): void {
        $primaryFieldName = EntityHelper::guessPrimaryFieldName(
            $this->entityHelper->getEntityFields($entityClassName),
        );

        if ($primaryFieldName) {
            $datasheet
                ->getColumn($primaryFieldName)
                ->setHandler(function ($value, $entity) use ($entityClassName, $router) {
                    return sprintf(
                        '<b><a href="%s">%s</a></b>',
                        $router->generate('admin_crud_view', [
                            'entityClassName' => $entityClassName,
                            'entityId' => $entity->getId(),
                        ]),
                        $value,
                    );
                });
        }
    }

    protected function addLinkToRelationField(
        DatasheetInterface $datasheet,
        string             $entityClassName,
        string             $fieldName,
        string             $fieldType,
        RouterInterface    $router,
    ): void {
        $relationClassName = $this->entityHelper->getRelationClassName($entityClassName, $fieldName);
        $relationPrimaryAttribute = $this->entityHelper->getEntityPrimaryAttribute($relationClassName);

        if ($fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::MANY_TO_ONE]) {
            $datasheet->getColumn($fieldName)->setHandler(function ($entity) use ($router, $relationClassName, $relationPrimaryAttribute) {
                if (empty($entity)) {
                    return '';
                }

                return sprintf(
                    '<a href="%s">#%s %s</a>',
                    $router->generate('admin_crud_view', [
                        'entityClassName' => $relationClassName,
                        'entityId' => $entity->getId(),
                    ]),
                    $entity->getId(),
                    ObjectHelper::getProperty($entity, $relationPrimaryAttribute),
                );
            });
        }

        if ($fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::MANY_TO_MANY]) {
            $datasheet->getColumn($fieldName)->setHandler(function ($entitys) use ($router, $relationClassName, $relationPrimaryAttribute) {
                if (empty($entitys)) {
                    return '';
                }
                $links = [];

                foreach ($entitys as $entity) {
                    $links[] = sprintf(
                        '<a href="%s">#%s %s</a>',
                        $router->generate('admin_crud_view', [
                            'entityClassName' => $relationClassName,
                            'entityId' => $entity->getId(),
                        ]),
                        $entity->getId(),
                        ObjectHelper::getProperty($entity, $relationPrimaryAttribute),
                    );
                }

                return implode(', ', $links);
            });
        }
    }
}
