<?php

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\ObjectHelper;
use AlexanderA2\SymfonyAdminBundle\Helper\EntityTranslationHelper;
use Doctrine\ORM\EntityManagerInterface;

class EntityDataBuilder
{
    public function __construct(
        protected EntityManagerInterface  $entityManager,
        protected EntityHelper            $entityHelper,
        protected EntityTranslationHelper $entityTranslationHelper,
    ) {
    }

    public function getData($object): array
    {
        $data = [];
        $entityClassName = get_class($object);

        foreach ($this->entityHelper->getEntityFields($entityClassName) as $fieldName => $fieldType) {
            $data[] = [
                'name' => $this->entityTranslationHelper->getTranslatedFieldName($entityClassName, $fieldName),
                'value' => $this->getReadableValue($object, $fieldName, $fieldType),
            ];
        }

        return $data;
    }

    public function getReadableValue(mixed $object, string $fieldName, string $fieldType): string
    {
        if (empty($object)) {
            return '';
        }

        return call_user_func_array(
            [EntityHelper::resolveDataTypeByFieldType($fieldType), 'toString'],
            [ObjectHelper::getProperty($object, $fieldName)],
        );
    }
}