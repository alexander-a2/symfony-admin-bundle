<?php

namespace AlexanderA2\AdminBundle\Datasheet\DataReader;

use AlexanderA2\AdminBundle\Datasheet\DatasheetBuildException;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use AlexanderA2\AdminBundle\Datasheet\Exception\NotSupportsException;
use AlexanderA2\AdminBundle\Datasheet\Helper\QueryBuilderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDataReader extends AbstractDataReader implements DataReaderInterface
{
    protected int $joinCount = 0;

    protected QueryBuilder $originalQueryBuilder;

    public function setSource(mixed $source): self
    {
        $this->source = $source;
        $this->originalQueryBuilder = clone $source;

        return $this;
    }

    public function readData(): ArrayCollection
    {
        $result = $this->getQueryBuilder()->getQuery()->getResult();

        return new ArrayCollection($result);
    }

    public function getTotalRecords(): int
    {
        $queryBuilder = clone($this->getQueryBuilder());
        $this->removeLeftJoins($queryBuilder);
        $queryBuilder
            ->resetDQLPart('select')
            ->addSelect(sprintf('COUNT(%s.id) AS total', QueryBuilderHelper::getPrimaryAlias($queryBuilder)))
//            ->resetDQLPart('groupBy')
            ->setFirstResult(null)
            ->setMaxResults(null);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->source;
    }

    protected function addJoin(string $primaryAlias, string $fieldName): string
    {
        $joins = $this->getQueryBuilder()->getDQLPart('join');
        if (!empty($joins[$primaryAlias])) {
            /** @var Join $join */
            foreach ($joins[$primaryAlias] as $join) {
                if ($join->getJoin() === $primaryAlias . '.' . $fieldName) {
                    throw new DatasheetBuildException('todo');
                }
            }
        }
        ++$this->joinCount;
        $joinAlias = 't' . $this->joinCount;
        $this->getQueryBuilder()->leftJoin($primaryAlias . '.' . $fieldName, $joinAlias);

        return $joinAlias;
    }

    protected function removeLeftJoins(QueryBuilder $queryBuilder): void
    {
        return;
        $joins = $queryBuilder->getDQLPart('join');
        $filteredJoins = [];

        foreach ($joins as $alias => $aliasJoins) {
            /** @var Join $join */
            foreach ($aliasJoins as $join) {
                if ($alias === QueryBuilderHelper::getPrimaryAlias($queryBuilder) && $join->getJoinType() === 'LEFT') {
                    continue;
                }
                $filteredJoins[] = $join;
            }
        }
        $queryBuilder->resetDQLPart('join');

        if (count($filteredJoins)) {
            throw new NotSupportsException();
        }
//      $queryBuilder->add($joinType, $alias, $joinData['alias'], $joinData['conditionType'], $joinData['condition']);
    }

    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getSource() instanceof QueryBuilder;
    }
}
