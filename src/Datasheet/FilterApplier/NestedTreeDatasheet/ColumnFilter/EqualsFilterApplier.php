<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\NestedTreeDatasheet\ColumnFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\EqualsFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\Filter\Applier\NestedTreeDatasheet\AbstractNestedTreeDatasheetFilterApplier;

class EqualsFilterApplier extends AbstractNestedTreeDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = EqualsFilter::class;

    public function apply(FilterApplierContext $context)
    {
    }
}
