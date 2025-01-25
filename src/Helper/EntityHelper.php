<?php

namespace AlexanderA2\AdminBundle\Helper;

use AlexanderA2\AdminBundle\Datasheet\DataType\BooleanDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\DateDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\DateTimeDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\FloatDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\IntegerDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\ObjectDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\ObjectsDataType;
use AlexanderA2\AdminBundle\Datasheet\DataType\StringDataType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use ReflectionClass;

class EntityHelper
{
    public const RELATION_FIELD_TYPES = [
        ClassMetadata::MANY_TO_ONE => 'many_to_one',
        ClassMetadata::MANY_TO_MANY => 'many_to_many',
    ];

    public const PRIMARY_FIELD_TYPICAL_NAMES = [
        'name',
        'firstname',
        'firstName',
        'first_name',
        'fullname',
        'full_name',
        'title',
        'label',
        'email',
    ];

    static array $entityListCached;

    protected array $entityMetadataCached = [];

    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    public static function get(EntityManagerInterface $entityManager): self
    {
        return new self($entityManager);
    }

    public function getEntityFields(string $className): array
    {
        $classMetadata = $this->getMetadata($className);
        $fields = [];

        foreach ($classMetadata->getFieldNames() as $fieldName) {
            $fieldMapping = $classMetadata->getFieldMapping($fieldName);
            $fields[$fieldName] = $fieldMapping['type'];
        }

        foreach ($classMetadata->getAssociationMappings() as $relation) {
            if (array_key_exists($relation['type'], self::RELATION_FIELD_TYPES)) {
                $fields[$relation['fieldName']] = self::RELATION_FIELD_TYPES[$relation['type']];
            }
        }
        $sortedFields = [];

        foreach ((new ReflectionClass($className))->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $fields)) {
                continue;
            }
            $sortedFields[$property->getName()] = $fields[$property->getName()];
        }

        return $sortedFields;
    }

    public function getFieldType(string $className, string $fieldName): string
    {
        if (in_array($fieldName, $this->getMetadata($className)->getFieldNames())) {
            return $this->getMetadata($className)->getFieldMapping($fieldName)['type'];
        }

        if (array_key_exists($fieldName, $this->getMetadata($className)->getAssociationMappings())) {
            $relation = $this->getMetadata($className)->getAssociationMapping($fieldName);

            return self::RELATION_FIELD_TYPES[$relation['type']];
        }

        throw new Exception('Field not found');
    }

    public function getRelationClassName(string $baseentityFqcn, string $relationFieldName): string
    {
        return $this
            ->getMetadata($baseentityFqcn)
            ->getAssociationMapping($relationFieldName)['targetEntity'];
    }

    public function getMetadata(string $entityFqcn): ClassMetadata
    {
        if (!array_key_exists($entityFqcn, $this->entityMetadataCached)) {
            $this->entityMetadataCached[$entityFqcn] = $this->entityManager->getClassMetadata($entityFqcn);
        }

        return $this->entityMetadataCached[$entityFqcn];
    }

    public function getEntityPrimaryAttribute(string $entityFqcn): ?string
    {
        $fields = $this->getEntityFields($entityFqcn);

        foreach (self::PRIMARY_FIELD_TYPICAL_NAMES as $name) {
            if (array_key_exists($name, $fields)) {
                return $name;
            }
        }

        return null;
    }

    public function getEntityList(): array
    {
        if (empty(self::$entityListCached)) {
            self::$entityListCached = $this->entityManager
                ->getConfiguration()
                ->getMetadataDriverImpl()
                ->getAllClassNames();
            sort(self::$entityListCached);
        }

        return self::$entityListCached;
    }

    public function getLabel(mixed $entity): string
    {
        if (empty($entity)) {
            return '';
        }

        if (method_exists($entity, '__toString')) {
            return (string) $entity;
        }
        $primaryField = self::guessPrimaryFieldName($this->getEntityFields(get_class($entity)), false);

        if ($primaryField) {
            return $entity->{'get' . ucfirst($primaryField)}();
        }

        return sprintf('%s #%d', StringHelper::getShortClassName($entity), $entity->getId());
    }

    public static function guessPrimaryFieldName(array $fields, bool $returnFallback = true): ?string
    {
        foreach (self::PRIMARY_FIELD_TYPICAL_NAMES as $name) {
            if (array_key_exists($name, $fields)) {
                return $name;
            }
        }

        return $returnFallback ? array_key_first($fields) : null;
    }

    public static function resolveDataTypeByFieldType(string $fieldType): string
    {
        return match ($fieldType) {
            'string',
            'text',
            'guid' => StringDataType::class,
            'smallint',
            'integer',
            'bigint' => IntegerDataType::class,
            'decimal',
            'float' => FloatDataType::class,
            'datetime',
            'datetimetz',
            'date_immutable' => DateTimeDataType::class,
            'date' => DateDataType::class,
            'boolean' => BooleanDataType::class,
            self::RELATION_FIELD_TYPES[ClassMetadata::MANY_TO_MANY] => ObjectsDataType::class,
            default => ObjectDataType::class,
        };
    }
}
