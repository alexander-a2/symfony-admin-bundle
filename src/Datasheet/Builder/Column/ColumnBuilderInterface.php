<?php

namespace AlexanderA2\AdminBundle\Datasheet\Builder\Column;

use AlexanderA2\AdminBundle\Datasheet\DatasheetInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool;

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface;
}
