<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContainsFilter extends AbstractFilter
{
    public const SHORT_NAME = 'has';

    public const FULL_NAME = 'contains';

    public function addForm(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('value', TextType::class, [
            'required' => false,
            'attr' => [
                'class' => 'form-control form-control-sm',
            ],
        ]);
    }
}
