<?php

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\ObjectHelper;
use Doctrine\ORM\EntityManagerInterface;

class EntityDataBuilder
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    ) {
    }

    public function getData($object): array
    {
        $data = [];

        foreach (EntityHelper::getEntityFields(get_class($object), $this->entityManager) as $fieldName => $fieldType) {
            $data[] = [
                'name' => $fieldName,
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