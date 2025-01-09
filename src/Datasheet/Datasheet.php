<?php

namespace AlexanderA2\AdminBundle\Datasheet;

use AlexanderA2\AdminBundle\Datasheet\DataReader\DataReaderInterface;
use AlexanderA2\AdminBundle\Datasheet\Filter\FilterInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Datasheet implements DatasheetInterface
{
    private const QUERY_KEYS = [
        'datasheet_filters' => 'df',
        'column_filters' => 'cf',
    ];

    protected ArrayCollection $data;

    protected array $columns;

    protected array $customizedColumns;

    protected array $removedColumns = [];

    protected DataReaderInterface $dataReader;

    protected int $totalRecordsUnfiltered;

    protected int $totalRecordsFiltered;

    protected array $filters = [];

    protected array $columnFilters = [];

    protected string $columnNamePrefix = '';

    public function __construct(
        protected mixed   $source,
        protected ?string $id = null,
    ) {
        if (empty($id)) {
            $this->id = $this->buildDatasheetId();
        }
    }

    public function getName(): string
    {
        return sprintf('%s%s', 'ds', $this->id);
    }

    public function getSource(): mixed
    {
        return $this->source;
    }

    public function getData(): ArrayCollection
    {
        return $this->data;
    }

    public function setData(ArrayCollection $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function addColumn(DatasheetColumn $column): self
    {
        $this->columns[$column->getName()] = $column;

        return $this;
    }

    public function getColumn(string $name): DatasheetColumnInterface
    {
        if (empty($this->customizedColumns[$name])) {
            $this->customizedColumns[$name] = new DatasheetColumnCustomized($name, null);
        }

        return $this->customizedColumns[$name];
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getCustomizedColumns(): array
    {
        return $this->customizedColumns ?? [];
    }

    public function getRemovedColumns(): array
    {
        return $this->removedColumns;
    }

    public function removeColumn(string $name): self
    {
        if (empty($this->columns)) {
            $this->removedColumns[] = $name;
        } else {
            unset($this->columns[$name]);
        }

        return $this;
    }

    public function getDataReader(): DataReaderInterface
    {
        return $this->dataReader;
    }

    public function setDataReader(DataReaderInterface $dataReader): void
    {
        $this->dataReader = $dataReader;
    }

    public function addFilter(FilterInterface $filter): self
    {
        $this->filters[$filter->getShortName()] = $filter;
        return $this;
    }

    public function getFilter(string $shortName): FilterInterface
    {
        return $this->filters[$shortName];
    }

    /** @return FilterInterface[] */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function addColumnFilter(string $columnName, FilterInterface $filter): self
    {
        $this->columnFilters[$columnName][] = $filter;

        return $this;
    }

    /** @return FilterInterface[] */
    public function getColumnFilters(string $columnName): array
    {
        return $this->columnFilters[$columnName] ?? [];
    }

    public function getQueryKey(string $name): string
    {
        return self::QUERY_KEYS[$name];
    }

    public function getTotalRecordsUnfiltered(): int
    {
        return $this->totalRecordsUnfiltered;
    }

    public function setTotalRecordsUnfiltered(int $totalRecordsUnfiltered): self
    {
        $this->totalRecordsUnfiltered = $totalRecordsUnfiltered;
        return $this;
    }

    public function getTotalRecordsFiltered(): int
    {
        return $this->totalRecordsFiltered;
    }

    public function setTotalRecordsFiltered(int $totalRecordsFiltered): self
    {
        $this->totalRecordsFiltered = $totalRecordsFiltered;
        return $this;
    }

    public function getColumnNamePrefix(): string
    {
        return $this->columnNamePrefix;
    }

    public function setColumnNamePrefix(string $columnNamePrefix): self
    {
        $this->columnNamePrefix = $columnNamePrefix;
        return $this;
    }

    protected function buildDatasheetId(): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);

        if (count($backtrace) >= 2) {
            return mb_substr(md5($backtrace[1]['file'] . PHP_EOL . $backtrace[1]['line']), 0, 3);
        }

        return mb_substr(md5($this->getSource()), 0, 3);
    }
}
