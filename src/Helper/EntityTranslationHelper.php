<?php

namespace AlexanderA2\SymfonyAdminBundle\Helper;

use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\StringHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Contracts\Translation\TranslatorInterface;

class EntityTranslationHelper
{
    private const FIELD_TITLE_PREFIX = 'entity';

    private const COMMON_FIELDS = [
        'id',
        'createdAt',
        'updatedAt',
    ];

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TranslatorInterface    $translator,
        protected EntityHelper           $entityHelper,
    ) {
    }

    public function getTranslatedFieldName(string $entityClassName, string $fieldName): string
    {
        return $this->translator->trans($this->getFieldTranslationId($entityClassName, $fieldName));
    }

    public function getFieldTranslationId(string $entityClassName, string $fieldName): string
    {
        $fieldType = $this->entityHelper->getFieldType($entityClassName, $fieldName);

        if (in_array($fieldType, EntityHelper::RELATION_FIELD_TYPES)) {
            return $this->getRelationFieldName($entityClassName, $fieldName, $fieldType);
        } else {
            return $this->getEntityFieldName($entityClassName, $fieldName);
        }
    }

    protected function getEntityFieldName(
        string $entityClassName,
        string $fieldName,
    ): string {
        if (in_array($fieldName, self::COMMON_FIELDS)) {
            return 'admin.entity.field.' . StringHelper::toSnakeCase($fieldName);
        }

        return implode('.', [
            self::FIELD_TITLE_PREFIX,
            StringHelper::toSnakeCase(StringHelper::getShortClassName($entityClassName)),
            'field',
            StringHelper::toSnakeCase($fieldName),
        ]);
    }

    protected function getRelationFieldName(
        string $entityClassName,
        string $fieldName,
        string $fieldType,
    ): string {
        return implode('.', [
            self::FIELD_TITLE_PREFIX,
            StringHelper::toSnakeCase(
                StringHelper::getShortClassName(
                    $this->entityHelper->getRelationClassName($entityClassName, $fieldName)
                )
            ),
            $fieldType === EntityHelper::RELATION_FIELD_TYPES[ClassMetadataInfo::MANY_TO_MANY] ? 'name_plural' : 'name_single',
        ]);
    }
}