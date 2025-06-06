<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\ArrayDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\PaginationFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\ArrayDatasheet\AbstractArrayDatasheetFilterApplier;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use Doctrine\DBAL\Query\QueryBuilder;

class PaginationFilterApplier extends AbstractArrayDatasheetFilterApplier
{
    public const string SUPPORTED_FILTER_CLASS = PaginationFilter::class;

    public function apply(FilterApplierContext $context): void
    {
        /** @var QueryBuilder $queryBuilder */
        $parameters = $context->getParameters();
        $context->getDataReader()->setSource(
            array_slice(
                $context->getDataReader()->getSource(),
                $parameters['recordsPerPage'] * ($parameters['currentPage'] - 1),
                $parameters['recordsPerPage'],
            )
        );
    }
}
