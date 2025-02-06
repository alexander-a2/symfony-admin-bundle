<?php

namespace AlexanderA2\AdminBundle\Datasheet;

use AlexanderA2\AdminBundle\Datasheet\DataReader\DataReaderInterface;
use AlexanderA2\AdminBundle\Datasheet\Filter\FilterInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

interface DatasheetInterface
{
    public const FORM_KEY_DATASHEET_FILTERS = 'df';
    public const FORM_KEY_COLUMN_FILTERS = 'cf';

    public function getSource(): mixed;

    public function getName(): string;

    public function getDataReader(): DataReaderInterface;

    public function setDataReader(DataReaderInterface $dataReader): void;

    public function getTotalRecordsUnfiltered(): int;

    public function setTotalRecordsUnfiltered(int $totalRecordsUnfiltered): self;

    public function getTotalRecordsFiltered(): int;

    public function setTotalRecordsFiltered(int $totalRecordsFiltered): self;

    public function addColumn(DatasheetColumn $column): self;

    public function getColumn(string $name): DatasheetColumnInterface;

    /** @return DatasheetColumn[] */
    public function getColumns(): array;

    public function removeColumn(string $name): self;

    /**
     * @return DatasheetColumnCustomized[]
     */
    public function getCustomizedColumns(): array;

    public function getRemovedColumns(): array;

    public function getQueryStringParameters();

    public function setQueryStringParameters(array $queryStringParameters = []): self;

    public function isDebug(): bool;

    public function setDebug(bool $debug): void;

    public function addFilter(FilterInterface $filter): self;

    public function getFilter(string $shortName): FilterInterface;

    /** @return FilterInterface[] */
    public function getFilters(): array;

    public function addColumnFilter(string $columnName, FilterInterface $filter): self;

    /** @return FilterInterface[] */
    public function getColumnFilters(string $columnName): array;
}
