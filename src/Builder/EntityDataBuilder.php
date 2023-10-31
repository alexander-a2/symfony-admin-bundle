<?php

namespace AlexanderA2\SymfonyAdminBundle\Builder;

use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;
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
//            $dataType = $this->entityHelper->resolveDataTypeByFieldType($fieldType);
//
//            if ($fieldName === 'id') {
//                continue;
//            }
            $data[] = [
                'name' => $fieldName,
                'value' => ObjectDataType::toString(ObjectHelper::getProperty($object, $fieldName)),
            ];
        }

        return $data;
    }
}