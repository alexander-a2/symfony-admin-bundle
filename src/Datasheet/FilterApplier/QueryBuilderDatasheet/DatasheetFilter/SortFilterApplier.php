<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\SortFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\AbstractQueryBuilderDatasheetFilterApplier;

class SortFilterApplier extends AbstractQueryBuilderDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = SortFilter::class;
    private const DIRECTION_DESC = 'desc';

    public function apply(FilterApplierContext $context)
    {
//        $parameters = $context->getFilter()->getParameters();
//        $source = $context->getDataReader()->getSource();
//
//        if (empty($parameters['by'])) {
//            return;
//        }
//        $sortBy = $parameters['by'];
//
//        if ($parameters['direction'] === self::DIRECTION_DESC) {
//            usort($source, function ($a, $b) use ($sortBy) {
//                return strcmp($b[$sortBy], $a[$sortBy]);
//            });
//        } else {
//            usort($source, function ($a, $b) use ($sortBy) {
//                return strcmp($a[$sortBy], $b[$sortBy]);
//            });
//        }
//        $context->getDataReader()->setSource($source);
    }
}
