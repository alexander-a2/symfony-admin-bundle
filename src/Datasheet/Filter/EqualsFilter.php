<?php

namespace AlexanderA2\AdminBundle\Datasheet\Filter;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EqualsFilter extends AbstractFilter
{
    public const string FULL_NAME = 'equals';

    public const string SHORT_NAME = 'eq';

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
