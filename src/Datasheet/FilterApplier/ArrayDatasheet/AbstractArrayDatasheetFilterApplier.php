<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\ArrayDatasheet;

use AlexanderA2\AdminBundle\Datasheet\DataReader\ArrayDataReader;
use AlexanderA2\AdminBundle\Datasheet\Filter\FilterInterface;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierInterface;

abstract class AbstractArrayDatasheetFilterApplier implements FilterApplierInterface
{
    public const SUPPORTED_FILTER_CLASS = FilterInterface::class;

    public function supports(FilterApplierContext $context): bool
    {
        return $context->getDataReader() instanceof ArrayDataReader
            && get_class($context->getFilter()) === static::SUPPORTED_FILTER_CLASS;
    }
}
