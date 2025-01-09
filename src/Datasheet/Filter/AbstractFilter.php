<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use Exception;

class AbstractFilter implements FilterInterface
{
    public const SHORT_NAME = __CLASS__;

    protected array $attributes = [];

    protected array $parameters = [];

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setParameter(string $parameterName, mixed $value): self
    {
        $this->parameters[$parameterName] = $value;

        return $this;
    }

    public function getParameter(string $parameterName)
    {
        if (!isset($this->attributes[$parameterName])) {
            throw new Exception();
        }

        return $this->parameters[$parameterName] ?? null;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getShortName(): string
    {
        return static::SHORT_NAME;
    }
}
