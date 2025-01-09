<?php

namespace AlexanderA2\AdminBundle\Datasheet\Resolver;

use AlexanderA2\AdminBundle\Datasheet\DatasheetBuildException;

abstract class AbstractServiceResolver
{
    public function __construct(
        protected iterable $services,
    ) {
    }

    public function resolve(mixed $context): mixed
    {
        foreach ($this->services as $service) {
            if ($service->supports($context)) {
                return $service;
            }
        }

        throw new DatasheetBuildException('Failed to resolve supported service via ' . get_class($this));
    }
}
