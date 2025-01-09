<?php

namespace AlexanderA2\AdminBundle\Datasheet\FilterApplier;

interface FilterApplierInterface
{
    public function supports(FilterApplierContext $context): bool;

    public function apply(FilterApplierContext $context);
}
