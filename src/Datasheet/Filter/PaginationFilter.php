<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use AlexanderA2\AdminBundle\Helper\StringHelper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class PaginationFilter extends AbstractFilter
{
    public const string SHORT_NAME = 'pgn';
    public const string FULL_NAME = 'pagination';
    public const string PARAMETER_RECORDS_PER_PAGE = 'recordsPerPage';
    public const string PARAMETER_CURRENT_PAGE = 'currentPage';

    public function getDefaultParameters(): array
    {
        return [
            self::PARAMETER_RECORDS_PER_PAGE => 10,
            self::PARAMETER_CURRENT_PAGE => 1,
        ];
    }

    public function addForm(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add(self::PARAMETER_RECORDS_PER_PAGE, IntegerType::class, [
            'label' => StringHelper::toReadable(self::PARAMETER_RECORDS_PER_PAGE),
            'required' => false,
        ]);
        $formBuilder->add(self::PARAMETER_CURRENT_PAGE, IntegerType::class, [
            'label' => StringHelper::toReadable(self::PARAMETER_CURRENT_PAGE),
            'required' => false,
        ]);
    }
}
