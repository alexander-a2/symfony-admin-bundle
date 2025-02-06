<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class PaginationFilter extends AbstractFilter
{
    public const SHORT_NAME = 'pgn';

    public const FULL_NAME = 'pagination';

    public function getDefaultParameters(): array
    {
        return [
            'recordsPerPage' => 10,
            'currentPage' => 1,
        ];
    }

    public function addForm(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('recordsPerPage', IntegerType::class, [
            'label' => 'Records per page',
            'required' => false,
        ]);
        $formBuilder->add('currentPage', IntegerType::class, [
            'label' => 'Current page',
            'required' => false,
        ]);
    }
}
