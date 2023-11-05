<?php

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\PhpDatasheet\Datasheet;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\ObjectHelper;
use AlexanderA2\SymfonyAdminBundle\Helper\EntityTranslationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Routing\RouterInterface;

class EntityDatasheetBuilder
{
    public function __construct(
        protected EntityManagerInterface  $entityManager,
        protected RouterInterface         $router,
        protected EntityTranslationHelper $entityTranslationHelper,
        protected EntityHelper            $entityHelper,
    ) {
    }

    public function build(
        string $entityClassName,
    ): DatasheetInterface {
        $datasheet = new Datasheet($this->entityManager->getRepository($entityClassName));
        $this->addLinkToPrimaryField($datasheet, $entityClassName, $this->router);
        foreach ($this->entityHelper->getEntityFields($entityClassName) as $fieldName => $fieldType) {
            $datasheet->getColumn($fieldName)->setTitle(
                $this->entityTranslationHelper->getTranslatedFieldName($entityClassName, $fieldName),
            );

            if (in_array($fieldType, EntityHelper::RELATION_FIELD_TYPES)) {
                $this->addLinkToRelationField($datasheet, $entityClassName, $fieldName, $fieldType, $this->router);
            }
        }

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

        if ($fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadataInfo::MANY_TO_ONE]) {
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

        if ($fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadataInfo::MANY_TO_MANY]) {
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