<?php

namespace AlexanderA2\AdminBundle\Datasheet\Resolver;

use AlexanderA2\AdminBundle\Datasheet\DataType\ObjectDataType;

class DataTypeResolver
{
    public function __construct(
        protected array $dataTypes
    ) {
    }

    public function guess($value): string
    {
        foreach ($this->dataTypes as $dataType) {
            if (call_user_func_array([$dataType, 'is'], [$value])) {
                return $dataType;
            }
        }

        return ObjectDataType::class;
    }
}
