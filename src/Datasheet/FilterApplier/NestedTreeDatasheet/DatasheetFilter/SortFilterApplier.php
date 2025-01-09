<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\NestedTreeDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\SortFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\Filter\Applier\NestedTreeDatasheet\AbstractNestedTreeDatasheetFilterApplier;

class SortFilterApplier extends AbstractNestedTreeDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = SortFilter::class;
    private const DIRECTION_DESC = 'desc';

    public function apply(FilterApplierContext $context)
    {
    }
}
