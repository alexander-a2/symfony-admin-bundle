<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use AlexanderA2\AdminBundle\Datasheet\DataType\IntegerDataType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SortFilter extends AbstractFilter
{
    public const SHORT_NAME = 'sort';

    public const FULL_NAME = 'sort';

    protected array $attributes = [
        'by' => IntegerDataType::class,
        'direction' => IntegerDataType::class,
    ];

    public function addForm(FormBuilderInterface $formBuilder): void
    {
        $formBuilder
            ->add('by', TextType::class, [
                'label' => 'Sort by',
                'required' => false,
            ])
            ->add('direction', TextType::class, [
                'label' => 'Sort direction',
                'required' => false,
            ]);
    }
}
