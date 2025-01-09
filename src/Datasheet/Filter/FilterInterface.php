<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

interface FilterInterface
{
    public function getAttributes(): array;

    public function getShortName(): string;

    public function setParameter(string $parameterName, mixed $value): self;

    public function getParameter(string $parameterName);

    public function getParameters(): array;
}
