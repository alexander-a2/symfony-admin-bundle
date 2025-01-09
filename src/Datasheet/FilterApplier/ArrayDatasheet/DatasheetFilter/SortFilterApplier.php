<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\ArrayDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\SortFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\ArrayDatasheet\AbstractArrayDatasheetFilterApplier;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;

class SortFilterApplier extends AbstractArrayDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = SortFilter::class;
    private const DIRECTION_DESC = 'desc';

    public function apply(FilterApplierContext $context)
    {
        $parameters = $context->getFilter()->getParameters();
        $source = $context->getDataReader()->getSource();

        if (empty($parameters['by'])) {
            return;
        }
        $sortBy = $parameters['by'];

        if ($parameters['direction'] === self::DIRECTION_DESC) {
            usort($source, function ($a, $b) use ($sortBy) {
                return strcmp($b[$sortBy], $a[$sortBy]);
            });
        } else {
            usort($source, function ($a, $b) use ($sortBy) {
                return strcmp($a[$sortBy], $b[$sortBy]);
            });
        }
        $context->getDataReader()->setSource($source);
    }

    function compareAscending($a, $b, $columnName): bool
    {
        return strcmp($a[$columnName], $b[$columnName]);
    }

    function compareDescending($a, $b, $columnName): bool
    {
        return strcmp($b[$columnName], $a[$columnName]);
    }
}
