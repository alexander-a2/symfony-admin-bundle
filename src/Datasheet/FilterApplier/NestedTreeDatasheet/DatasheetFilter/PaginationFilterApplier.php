<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\NestedTreeDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\PaginationFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\Filter\Applier\NestedTreeDatasheet\AbstractNestedTreeDatasheetFilterApplier;

class PaginationFilterApplier extends AbstractNestedTreeDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = PaginationFilter::class;

    public function apply(FilterApplierContext $context)
    {
    }
}
