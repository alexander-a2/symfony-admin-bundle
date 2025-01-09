<?php

namespace AlexanderA2\AdminBundle\Datasheet\DataReader;

use AlexanderA2\AdminBundle\Datasheet\DataReader\DataReaderInterface;
use AlexanderA2\AdminBundle\Datasheet\DataReader\QueryBuilderDataReader;
use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use Doctrine\ORM\EntityRepository;

class EntityDataReader extends QueryBuilderDataReader implements DataReaderInterface
{
    public function setSource(mixed $source): self
    {
        parent::setSource($source->createQueryBuilder('e'));

        return $this;
    }

    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getSource() instanceof EntityRepository;
    }

    public static function getDefaultPriority(): int
    {
        return 600;
    }
}
