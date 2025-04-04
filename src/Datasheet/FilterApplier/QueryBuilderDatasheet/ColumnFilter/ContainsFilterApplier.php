<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\ColumnFilter;

use AlexanderA2\AdminBundle\Datasheet\Filter\ContainsFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\QueryBuilderDatasheet\AbstractQueryBuilderDatasheetFilterApplier;
use AlexanderA2\AdminBundle\Datasheet\Helper\QueryBuilderHelper;

class ContainsFilterApplier extends AbstractQueryBuilderDatasheetFilterApplier
{
    public const string SUPPORTED_FILTER_CLASS = ContainsFilter::class;

    public function apply(FilterApplierContext $context): void
    {
        $parameters = $context->getParameters();

        if (empty($parameters['value'])) {
            return;
        }
        $queryBuilder = $context->getDataReader()->getSource();
        $queryBuilder->andWhere(
            sprintf(
                '%s.%s LIKE :%s',
                QueryBuilderHelper::getPrimaryAlias($queryBuilder),
                $context->getDatasheetColumn()->getName(),
                $this->getUniqueDqlParameterName($context),
            )
        );
        $queryBuilder->setParameter(
            $this->getUniqueDqlParameterName($context),
            sprintf('%%%s%%', $parameters['value']),
        );
    }
}
