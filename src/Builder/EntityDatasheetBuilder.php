<?php

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\PhpDatasheet\Datasheet;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\ObjectHelper;
use AlexanderA2\PhpDatasheet\Helper\StringHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EntityDatasheetBuilder
{
    private const FIELD_TITLE_PREFIX = 'entity';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RouterInterface        $router,
        protected TranslatorInterface    $translator,
    ) {
    }

    public function build($entityClassName): DatasheetInterface
    {
        $datasheet = new Datasheet($this->entityManager->getRepository($entityClassName));
        $this->addLinkToPrimaryField($datasheet, $entityClassName, $this->router);
        $fieldTitlePrefix = implode('.', [
            self::FIELD_TITLE_PREFIX,
            StringHelper::toSnakeCase(StringHelper::getShortClassName($entityClassName)),
            'field',
        ]);

        foreach (EntityHelper::getEntityFields($entityClassName, $this->entityManager) as $fieldName => $fieldType) {
            $datasheet->getColumn($fieldName)
                ->setTitle($this->translator->trans($fieldTitlePrefix . '.' . StringHelper::toSnakeCase($fieldName)));

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
            EntityHelper::getEntityFields($entityClassName, $this->entityManager),
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
        $relationClassName = EntityHelper::getRelationClassName($entityClassName, $fieldName, $this->entityManager);
        $relationPrimaryAttribute = EntityHelper::getEntityPrimaryAttribute($relationClassName, $this->entityManager);

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