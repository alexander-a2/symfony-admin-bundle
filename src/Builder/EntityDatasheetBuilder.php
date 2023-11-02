<?php

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\PhpDatasheet\Datasheet;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\Helper\ArrayHelper;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Routing\RouterInterface;

class EntityDatasheetBuilder
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RouterInterface        $router,
    ) {
    }

    public function build($entityClassName): DatasheetInterface
    {
        $datasheet = new Datasheet($this->entityManager->getRepository($entityClassName));
        $this->addLinkToPrimaryField($datasheet, $entityClassName, $this->router);

        foreach (EntityHelper::getEntityFields($entityClassName, $this->entityManager) as $fieldName => $fieldType) {
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
                ->setHandler(function ($value, $record) use ($entityClassName, $router) {
                    return sprintf(
                        '<b><a href="%s">%s</a></b>',
                        $router->generate('admin_crud_view', [
                            'objectClassName' => $entityClassName,
                            'objectId' => $record['id'],
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

        if ($fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadataInfo::MANY_TO_ONE]) {
            $datasheet->getColumn($fieldName)->setHandler(function ($value, $record) use ($router, $relationClassName) {
                return sprintf(
                    '<a href="%s">#%s %s</a>',
                    $router->generate('admin_crud_view', [
                        'objectClassName' => $relationClassName,
                        'objectId' => $record['id'],
                    ]),
                    $record['id'],
                    $record[ArrayHelper::getPrimaryKey($record)],
                );
            });
        }
    }
}