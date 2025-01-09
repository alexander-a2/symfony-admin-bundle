<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\ArrayDatasheet\ColumnFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\EqualsFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\ArrayDatasheet\AbstractArrayDatasheetFilterApplier;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;

class EqualsFilterApplier extends AbstractArrayDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = EqualsFilter::class;

    public function apply(FilterApplierContext $context)
    {
        $filterValue = $context->getFilter()->getParameter('value');

        if (empty($filterValue)) {
            return;
        }
        $columnName = $context->getDatasheetColumn()->getName();
        $filteredData = array_filter($context->getDataReader()->getSource(), function ($row) use ($columnName, $filterValue) {
            return $row[$columnName] == $filterValue;
        });
        $context->getDataReader()->setSource($filteredData);
    }
}
