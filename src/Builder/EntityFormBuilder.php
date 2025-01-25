<?php
declare(strict_types=1);

namespace AlexanderA2\AdminBundle\Builder;

use AlexanderA2\AdminBundle\Helper\EntityHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EntityFormBuilder
{
    private const FORM_FIELDS_MAPPING = [
        'date' => DateType::class,
        'datetime' => DateTimeType::class,
        'many_to_one' => EntityType::class,
        'many_to_many' => EntityType::class,
        'json' => null,
    ];

    private const FIELDS_NOT_FOR_EDIT = [
        'id',
        'createdAt',
        'updatedAt',
    ];

    public function __construct(
        protected FormFactoryInterface     $formFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected EntityHelper             $entityHelper,
    ) {
    }

    public function get(mixed $object): FormInterface
    {
        $entityClassName = get_class($object);
        $formBuilder = $this->formFactory->createBuilder(FormType::class, $object, [
            'data_class' => get_class($object),
            'csrf_protection' => false,
        ]);
        foreach ($this->entityHelper->getEntityFields($entityClassName) as $fieldName => $fieldType) {
            if (in_array($fieldName, self::FIELDS_NOT_FOR_EDIT)) {
                continue;
            }

            if (array_key_exists($fieldType, self::FORM_FIELDS_MAPPING) && is_null(self::FORM_FIELDS_MAPPING[$fieldType])) {
                continue;
            }
            $type = self::FORM_FIELDS_MAPPING[$fieldType] ?? null;
            $options = array_merge([
                'label' => $fieldName,
            ], $this->getFieldSpecificOptions($fieldName, $fieldType, $object));
            $formBuilder->add($fieldName, $type, $options);
        }
        $formBuilder->add('submit', SubmitType::class, [
            'label' => 'a2platform.admin.controls.save',
            'attr' => [
                'class' => 'btn btn-primary px-5',
            ],
        ]);
//        $this->eventDispatcher->dispatch(new EntityFormBuildEvent($entityClassName, $formBuilder));

        return $formBuilder->getForm();
    }

    protected function getFieldSpecificOptions(string $fieldName, string $fieldType, mixed $object): array
    {
        if (in_array($fieldType, ['date', 'datetime', 'datetimez'])) {
            return [
                'widget' => 'single_text',
            ];
        }

        if (in_array($fieldType, ['many_to_one', 'many_to_many'])) {
            $relationClassName = $this->entityHelper->getRelationClassName(get_class($object), $fieldName);
            $options = [
                'class' => $relationClassName,
                'empty_data' => null,
                'required' => false,
                'attr' => [
                    'class' => 'form-control selectpicker',
                ],
            ];

            if (!method_exists($relationClassName, '__toString')) {
                $options['choice_label'] = $this->entityHelper
                    ->getEntityPrimaryAttribute($relationClassName) ?? 'id';
            }

            if ($fieldType === 'many_to_many') {
                $options['multiple'] = true;
            }

            return $options;
        }

        return [];
    }
}
