<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet;

use AlexanderA2\AdminBundle\Datasheet\DataReader\QueryBuilderDataReader;
use AlexanderA2\AdminBundle\Datasheet\Filter\FilterInterface;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierInterface;

abstract class AbstractQueryBuilderDatasheetFilterApplier implements FilterApplierInterface
{
    public const SUPPORTED_FILTER_CLASS = FilterInterface::class;

    public function supports(FilterApplierContext $context): bool
    {
        return $context->getDataReader() instanceof QueryBuilderDataReader
            && get_class($context->getFilter()) === static::SUPPORTED_FILTER_CLASS;
    }

    protected function getUniqueDqlParameterName(FilterApplierContext $context, string $parameterName = 'value'): string
    {
        return sprintf(
            '%sFilter%s%s',
            $context->getDatasheetColumn()->getName(),
            mb_ucfirst($context->getFilter()->getFullName()),
            ucfirst($parameterName),
        );
    }
}
