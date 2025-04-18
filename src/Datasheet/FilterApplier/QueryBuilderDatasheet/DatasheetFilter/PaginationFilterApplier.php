<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\DatasheetFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\PaginationFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\AbstractQueryBuilderDatasheetFilterApplier;
use Doctrine\DBAL\Query\QueryBuilder;

class PaginationFilterApplier extends AbstractQueryBuilderDatasheetFilterApplier
{
    public const string SUPPORTED_FILTER_CLASS = PaginationFilter::class;

    public function apply(FilterApplierContext $context): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $context->getDataReader()->getSource();
        $parameters = $context->getParameters();
        $queryBuilder->setFirstResult($parameters[PaginationFilter::PARAMETER_RECORDS_PER_PAGE] * ($parameters[PaginationFilter::PARAMETER_CURRENT_PAGE] - 1));
        $queryBuilder->setMaxResults($parameters[PaginationFilter::PARAMETER_RECORDS_PER_PAGE]);
    }
}
