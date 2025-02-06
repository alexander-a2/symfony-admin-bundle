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
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DatasheetBuilder
{
    public function __construct(
        protected DataReaderResolver $dataReaderResolver,
        protected ColumnBuilderResolver $columnBuilderResolver,
        protected FilterApplierResolver $filterApplierResolver,
        protected FormFactoryInterface $formFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected RequestStack $requestStack,
    ) {
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
        $this->buildForm($datasheet);
        $this->fillFiltersWithRequestedParams($datasheet, $parameters);
        $this->buildUnfilteredTotals($datasheet);
        $this->applyFilters($datasheet);
        $this->buildFilteredTotals($datasheet);
        $this->readData($datasheet);

        return $event->getDatasheet();
    }

    protected function buildForm(DatasheetInterface $datasheet): void
    {
        $rootFormBuilder = $this->formFactory->createBuilder(FormType::class, [
            'csrf_protection' => false,
            'method' => Request::METHOD_GET,
        ]);
        $datasheetFormBuilder = $rootFormBuilder
            ->add($datasheet->getName(), FormType::class)
            ->get($datasheet->getName());
        $datasheetFilters = $datasheetFormBuilder
            ->add(DatasheetInterface::FORM_KEY_DATASHEET_FILTERS, FormType::class)
            ->get(DatasheetInterface::FORM_KEY_DATASHEET_FILTERS);

        foreach ($datasheet->getFilters() as $filter) {
            $filter->addForm($datasheetFilters
                ->add($filter->getShortName(), FormType::class, [
                    'data' => $filter->getDefaultParameters(),
                ])
                ->get($filter->getShortName())
            );
        }
        $form = $rootFormBuilder->getForm();
        $form->handleRequest($this->requestStack->getMainRequest());
        $datasheet->setForm($form);
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
    }

    protected function buildUnfilteredTotals(DatasheetInterface $datasheet): void
    {
        $datasheet->setTotalRecordsUnfiltered($datasheet->getDataReader()->getTotalRecords());
    }

    protected function applyFilters(DatasheetInterface $datasheet): void
    {
        $formData = $datasheet->getForm()->getData()[$datasheet->getName()] ?? [];
        foreach ($datasheet->getFilters() as $filter) {
            $filterParameters = $formData[DatasheetInterface::FORM_KEY_DATASHEET_FILTERS][$filter->getShortName()] ?? $filter->getDefaultParameters();

            if (empty($filterParameters)) {
                continue;
            }
            $context = new FilterApplierContext($datasheet->getDataReader(), $filter, $filterParameters);
            $this->filterApplierResolver
                ->resolve($context)
                ->apply($context);
        }

        return;

        foreach ($datasheet->getColumns() as $column) {
            foreach ($datasheet->getColumnFilters($column->getName()) as $filter) {
                $context = new FilterApplierContext(
                    $datasheet->getDataReader(),
                    $filter,
                    $formData[DatasheetInterface::FORM_KEY_DATASHEET_FILTERS][$filter->getShortName()] ?? [],
                    $column,
                );

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
