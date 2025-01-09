<?php

namespace AlexanderA2\AdminBundle\Datasheet\DataReader;

use AlexanderA2\AdminBundle\Datasheet\DataReader\AbstractDataReader;
use AlexanderA2\AdminBundle\Datasheet\DataReader\DataReaderInterface;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class NestedTreeDataReader extends AbstractDataReader implements DataReaderInterface
{
    protected ArrayCollection $items;

    protected NestedTreeRepository $repository;

    public function setSource(mixed $source): self
    {
        $this->repository = $source;
        $this->source = $source->createQueryBuilder('a');

        return $this;
    }

    public function readData(): ArrayCollection
    {
        $this->items = new ArrayCollection();
        $rootNodes = $this->getRepository()->getRootNodes();
        $this->buildTree($rootNodes);

        return $this->items;
    }

    public function getTotalRecords(): int
    {
        return $this->repository->count([]);
    }

    public function getRepository(): NestedTreeRepository
    {
        return $this->repository;
    }

    protected function buildTree($nodes)
    {
        foreach ($nodes as $node) {
            $this->items->add($node);
            $this->buildTree($node->getChildren());
        }
    }

    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getSource() instanceof NestedTreeRepository;
    }

    public static function getDefaultPriority(): int
    {
        return 620;
    }
}
