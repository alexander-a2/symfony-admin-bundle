<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\PaginationFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\AbstractQueryBuilderDatasheetFilterApplier;
use Doctrine\DBAL\Query\QueryBuilder;

class PaginationFilterApplier extends AbstractQueryBuilderDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = PaginationFilter::class;

    public function apply(FilterApplierContext $context)
    {
        $parameters = $context->getFilter()->getParameters();

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $context->getDataReader()->getSource();
        $queryBuilder->setFirstResult($parameters['recordsPerPage'] * ($parameters['currentPage'] - 1));
        $queryBuilder->setMaxResults($parameters['recordsPerPage']);
    }
}
