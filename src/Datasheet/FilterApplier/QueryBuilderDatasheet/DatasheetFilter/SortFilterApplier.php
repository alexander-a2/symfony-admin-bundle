<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\SortFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\AbstractQueryBuilderDatasheetFilterApplier;
use AlexanderA2\AdminBundle\Datasheet\Helper\QueryBuilderHelper;

class SortFilterApplier extends AbstractQueryBuilderDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = SortFilter::class;
    protected const DIRECTION_ASC = 'asc';
    protected const DIRECTION_DESC = 'desc';

    public function apply(FilterApplierContext $context): void
    {
        $parameters = $context->getFilter()->getParameters();
        $source = $context->getDataReader()->getSource();

        if (empty($parameters['by'])) {
            return;
        }
        $sortBy = $parameters['by'];
        $sortDirection = $parameters['direction'] === self::DIRECTION_DESC ? self::DIRECTION_DESC : self::DIRECTION_ASC;
        $queryBuilder = $context->getDataReader()->getSource();
        $queryBuilder->resetDQLPart('orderBy');
        $queryBuilder->orderBy(QueryBuilderHelper::getPrimaryAlias($queryBuilder) . '.' . $sortBy, $sortDirection);
    }
}
