<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\ColumnFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\ContainsFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\AbstractQueryBuilderDatasheetFilterApplier;

class ContainsFilterApplier extends AbstractQueryBuilderDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = ContainsFilter::class;

    public function apply(FilterApplierContext $context)
    {
//        $filterValue = $context->getFilter()->getParameter('value');
//
//        if (empty($filterValue)) {
//            return;
//        }
//        $columnName = $context->getDatasheetColumn()->getName();
//        $filteredData = array_filter($context->getDataReader()->getSource(), function ($row) use ($columnName, $filterValue) {
//            return stripos($row[$columnName], $filterValue) !== false;
//        });
//        $context->getDataReader()->setSource($filteredData);
    }
}
