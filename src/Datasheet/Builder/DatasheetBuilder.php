<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\Builder;

use AlexanderA2\AdminBundle\Datasheet\Builder\Column\ColumnBuilderInterface;
use AlexanderA2\AdminBundle\Datasheet\DataReader\DataReaderInterface;
use AlexanderA2\AdminBundle\Datasheet\DatasheetColumnInterface;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use AlexanderA2\AdminBundle\Datasheet\Filter\FilterInterface;
use AlexanderA2\AdminBundle\Datasheet\Filter\PaginationFilter;
use AlexanderA2\AdminBundle\Datasheet\Filter\SortFilter;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\AdminBundle\Datasheet\FilterApplier\FilterApplierInterface;
use AlexanderA2\AdminBundle\Helper\ObjectHelper;
use AlexanderA2\AdminBundle\Datasheet\Resolver\ColumnBuilderResolver;
use AlexanderA2\AdminBundle\Datasheet\Resolver\DataReaderResolver;
use AlexanderA2\AdminBundle\Datasheet\Resolver\FilterApplierResolver;
use AlexanderA2\AdminBundle\Datasheet\Event\DatasheetBuildEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

class DatasheetBuilder
{
    public function __construct(
        protected DataReaderResolver    $dataReaderResolver,
        protected ColumnBuilderResolver $columnBuilderResolver,
        protected FilterApplierResolver $filterApplierResolver,
    ) {
    }

    protected EventDispatcherInterface $eventDispatcher;

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function build(DatasheetInterface $datasheet, array $parameters = []): DatasheetInterface
    {
        $event = new DatasheetBuildEvent($datasheet);
        $this->eventDispatcher->dispatch($event);
        $this->resolveDataReader($datasheet);
        $this->buildColumns($datasheet);
        $this->updateCustomizedColumns($datasheet);
        $this->removeColumns($datasheet);
        $this->attachDatasheetFilters($datasheet);
        $this->attachColumnFilters($datasheet);
        $this->fillFiltersWithRequestedParams($datasheet, $parameters);
        $this->buildUnfilteredTotals($datasheet);
        $this->applyFilters($datasheet);
        $this->buildFilteredTotals($datasheet);
        $this->readData($datasheet);

        return $event->getDatasheet();
    }

    protected function resolveDataReader(DatasheetInterface $datasheet): void
    {
        /** @var DataReaderInterface $dataReader */
        $dataReader = $this->dataReaderResolver->resolve($datasheet);
        $dataReader->setSource($datasheet->getSource());
        $datasheet->setDataReader($dataReader);
    }

    protected function buildColumns(DatasheetInterface $datasheet): void
    {
        /** @var ColumnBuilderInterface $columnBuilder */
        $columnBuilder = $this->columnBuilderResolver->resolve($datasheet);
        $columnBuilder->addColumnsToDatasheet($datasheet);
    }

    protected function updateCustomizedColumns(DatasheetInterface $datasheet): void
    {
        foreach ($datasheet->getCustomizedColumns() as $columnName => $customizedColumn) {
            if (!array_key_exists($columnName, $datasheet->getColumns())) {
                continue;
            }
            /** @var DatasheetColumnInterface $column */
            $column = $datasheet->getColumns()[$columnName];

            foreach ($customizedColumn->getCustomizedAttributes() as $attribute) {
                ObjectHelper::setProperty(
                    $column,
                    $attribute,
                    ObjectHelper::getProperty($customizedColumn, $attribute)
                );
            }
        }
    }

    protected function removeColumns(DatasheetInterface $datasheet): void
    {
        foreach ($datasheet->getRemovedColumns() as $columnName) {
            $datasheet->removeColumn($columnName);
        }
    }

    protected function attachDatasheetFilters(DatasheetInterface $datasheet): void
    {
        $datasheet->addFilter(new PaginationFilter());
        $datasheet->addFilter(new SortFilter());
    }

    protected function attachColumnFilters(DatasheetInterface $datasheet): void
    {
        foreach ($datasheet->getColumns() as $column) {
            /** @var FilterInterface $filter */
            foreach (call_user_func([$column->getDataType(), 'getFilters']) as $filter) {
                $datasheet->addColumnFilter($column->getName(), new $filter());
            }
        }
    }

    protected function fillFiltersWithRequestedParams(DatasheetInterface $datasheet, array $parameters = []): void
    {
        foreach ($datasheet->getFilters() as $filter) {
            foreach ($filter->getAttributes() as $attributeName => $attributeDataType) {
                // todo: validate/filter data type
                if (isset($parameters[$datasheet->getQueryKey('datasheet_filters')][$filter->getShortName()][$attributeName])) {
                    $value = $parameters[$datasheet->getQueryKey('datasheet_filters')][$filter->getShortName()][$attributeName];
                    $filter->setParameter($attributeName, $value);
                }
            }
        }

        foreach ($datasheet->getColumns() as $column) {
            foreach ($datasheet->getColumnFilters($column->getName()) as $filter) {
                foreach ($filter->getAttributes() as $attributeName => $attributeDataType) {
                    // todo: validate/filter data type
                    if (isset($parameters[$datasheet->getQueryKey('column_filters')][$column->getName()][$filter->getShortName()][$attributeName])) {
                        $value = $parameters[$datasheet->getQueryKey('column_filters')][$column->getName()][$filter->getShortName()][$attributeName];
                        $filter->setParameter($attributeName, $value);
                    }
                }
            }
        }
    }

    protected function buildUnfilteredTotals(DatasheetInterface $datasheet): void
    {
        $datasheet->setTotalRecordsUnfiltered($datasheet->getDataReader()->getTotalRecords());
    }

    protected function applyFilters(DatasheetInterface $datasheet): void
    {
        foreach ($datasheet->getFilters() as $filter) {
            $context = new FilterApplierContext($datasheet->getDataReader(), $filter);

            /** @var FilterApplierInterface $filterApplier */
            $this->filterApplierResolver
                ->resolve($context)
                ->apply($context);
        }

        foreach ($datasheet->getColumns() as $column) {
            foreach ($datasheet->getColumnFilters($column->getName()) as $filter) {
                $context = new FilterApplierContext($datasheet->getDataReader(), $filter, $column);

                /** @var FilterApplierInterface $filterApplier */
                $this->filterApplierResolver
                    ->resolve($context)
                    ->apply($context);
            }
        }
    }

    protected function buildFilteredTotals(DatasheetInterface $datasheet): void
    {
        $datasheet->setTotalRecordsFiltered($datasheet->getDataReader()->getTotalRecords());
    }

    protected function readData(DatasheetInterface $datasheet): void
    {
        $datasheet->setData($datasheet->getDataReader()->readData());
    }
}
