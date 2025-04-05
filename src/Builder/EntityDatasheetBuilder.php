<?php

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Datasheet\Datasheet;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use AlexanderA2\AdminBundle\Datasheet\DataType\EmptyDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\StringDataType;
use AlexanderA2\AdminBundle\Event\EntityDatasheetBuildEvent;
use AlexanderA2\AdminBundle\Helper\EntityHelper;
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
            if (in_array($fieldType, [
                EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::ONE_TO_ONE],
                EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::MANY_TO_ONE],
            ])) {
                $this->decorateSingleEntityColumn($datasheet, $entityFqcn, $fieldName);
            }
            if (in_array($fieldType, [
                EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::ONE_TO_MANY],
                EntityHelper::RELATION_FIELD_TYPES[ClassMetadata::MANY_TO_MANY],
            ])) {
                $this->decorateMultipleEntityColumn($datasheet, $entityFqcn, $fieldName, !empty($entityId));
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

    protected function decorateSingleEntityColumn(DatasheetInterface $datasheet, string $entityFqcn, string $fieldName): void
    {
        $relationFqcn = $this->entityHelper->getRelationClassName($entityFqcn, $fieldName);
        $router = $this->router;
        $entityHelper = $this->entityHelper;

        $datasheet->getColumn($fieldName)
            ->setHandler(function ($entity) use ($router, $relationFqcn, $entityHelper) {
                if (EmptyDataType::is($entity)) {
                    return EmptyDataType::toFormatted(null);
                }

                return sprintf(
                    '<a href="%s">%s</a>',
                    $router->generate('admin_crud_view', [
                        'entityFqcn' => $relationFqcn,
                        'entityId' => $entity->getId(),
                    ]),
                    StringDataType::toString($entityHelper->getLabel($entity)),
                );
            });
    }

    protected function decorateMultipleEntityColumn(DatasheetInterface $datasheet, string $entityFqcn, string $fieldName, bool $showFullList = false): void
    {
        $relationFqcn = $this->entityHelper->getRelationClassName($entityFqcn, $fieldName);
        $router = $this->router;
        $entityHelper = $this->entityHelper;

        $datasheet->getColumn($fieldName)
            ->setHandler(function ($entities) use ($router, $relationFqcn, $entityHelper, $showFullList) {
                if (count($entities) === 0) {
                    return EmptyDataType::toFormatted(null);
                }

                $output = [];

                for ($i = 0; $i < ($showFullList ? count($entities) : 1); $i++) {
                    $output[] = sprintf(
                        '<a href="%s">%s</a>',
                        $router->generate('admin_crud_view', [
                            'entityFqcn' => $relationFqcn,
                            'entityId' => $entities[$i]->getId(),
                        ]),
                        StringDataType::toString($entityHelper->getLabel($entities[$i])),
                    );
                }
                $output = implode(', ', $output);

                if (!$showFullList && count($entities) > 1) {
                    $output .= ' (+' . (count($entities) - 1) . ')';
                }

                return $output;
            });
    }
}
