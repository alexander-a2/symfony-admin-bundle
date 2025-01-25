<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Datasheet\Datasheet;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use AlexanderA2\AdminBundle\Datasheet\DataType\EmptyDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\StringDataType;
use AlexanderA2\AdminBundle\Event\EntityDatasheetBuildEvent;
use AlexanderA2\AdminBundle\Helper\EntityHelper;
use AlexanderA2\AdminBundle\Helper\ObjectHelper;
use AlexanderA2\AdminBundle\Helper\StringHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EntityDatasheetBuilder
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RouterInterface $router,
        protected EntityHelper $entityHelper,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function buildDatasheet(string $entityFqcn, int $entityId = null): DatasheetInterface
    {
        // Create basic datasheet
        if (!empty($entityId)) {
            $source = $this->entityManager
                ->getRepository($entityFqcn)
                ->createQueryBuilder('e')
                ->where('e.id = :id')
                ->setParameter('id', $entityId);
        } else {
            $source = $this->entityManager->getRepository($entityFqcn);
        }
        $datasheet = new Datasheet($source);

        // Decorate fields
        foreach ($this->entityHelper->getEntityFields($entityFqcn) as $fieldName => $fieldType) {
            $datasheet
                ->getColumn($fieldName)
                ->setTitle(StringHelper::toReadable($fieldName));
        }

        // Decorate primary field
        $this->highlightPrimaryField($datasheet, $entityFqcn, empty($entityId));

        // Decorate relation fields
        foreach ($this->entityHelper->getEntityFields($entityFqcn) as $fieldName => $fieldType) {
            if (in_array($fieldType, EntityHelper::RELATION_FIELD_TYPES)) {
                $this->addLinkToRelationField($datasheet, $entityFqcn, $fieldName, $fieldType);
            }
        }
        $this->eventDispatcher->dispatch(new EntityDatasheetBuildEvent($entityFqcn, $datasheet));

        return $datasheet;
    }

    protected function highlightPrimaryField(DatasheetInterface $datasheet, string $entityFqcn, bool $addLink = false): void
    {
        $primaryFieldName = EntityHelper::guessPrimaryFieldName($this->entityHelper->getEntityFields($entityFqcn));
        $router = $this->router;
        $datasheet
            ->getColumn($primaryFieldName)
            ->setHandler(function ($value, $entity) use ($entityFqcn, $router, $addLink) {
                if (EmptyDataType::is($value)) {
                    $value = '<i>' . EmptyDataType::toString($value) . '</i>';
                } else {
                    $value = '<b>' . $value . '</b>';
                }
                $value = StringDataType::toFormatted($value);

                if (!$addLink) {
                    return $value;
                }

                return sprintf(
                    '<a href="%s">%s</a>',
                    $router->generate('admin_crud_view', [
                        'entityFqcn' => $entityFqcn,
                        'entityId' => $entity->getId(),
                    ]),
                    $value,
                );
            });
    }

    protected function addLinkToRelationField(DatasheetInterface $datasheet, string $entityFqcn, string $fieldName, string $fieldType): void
    {
        $relationFqcn = $this->entityHelper->getRelationClassName($entityFqcn, $fieldName);
        $router = $this->router;
        $entityHelper = $this->entityHelper;

        if ($fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::MANY_TO_ONE]) {
            $datasheet->getColumn($fieldName)
                ->setHandler(function ($entity) use ($router, $relationFqcn, $entityHelper) {
                    if (EmptyDataType::is($entity)) {
                        return EmptyDataType::toFormatted($entity);
                    }

                    return sprintf(
                        '<a href="%s">%s</a>',
                        $router->generate('admin_crud_view', [
                            'entityFqcn' => $relationFqcn,
                            'entityId' => $entity->getId(),
                        ]),
                        StringDataType::toFormatted($entityHelper->getLabel($entity)),
                    );
                });
        }

//        if ($fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::MANY_TO_MANY]) {
//            $datasheet->getColumn($fieldName)->setHandler(function ($entities) use ($router, $relationClassFqcn, $relationPrimaryAttribute) {
//                if (empty($entities)) {
//                    return '';
//                }
//                $links = [];
//
//                foreach ($entities as $entity) {
//                    $links[] = sprintf(
//                        '<a href="%s">#%s %s</a>',
//                        $router->generate('admin_crud_view', [
//                            'entityFqcn' => $relationClassFqcn,
//                            'entityId' => $entity->getId(),
//                        ]),
//                        $entity->getId(),
//                        ObjectHelper::getProperty($entity, $relationPrimaryAttribute),
//                    );
//                }
//
//                return implode(', ', $links);
//            });
//        }
    }
}
