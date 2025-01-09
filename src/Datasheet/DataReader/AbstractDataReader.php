<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Datasheet\DataReader;

abstract class AbstractDataReader
{
    protected mixed $source;

    public function getSource(): mixed
    {
        return $this->source;
    }

    public function setSource(mixed $source): self
    {
        $this->source = $source;
        return $this;
    }
}
