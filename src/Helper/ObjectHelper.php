<?php

namespace AlexanderA2\AdminBundle\Helper;

use AlexanderA2\ExtendableEntityBundle\ExtendableEntity;
use AlexanderA2\ExtendableEntityBundle\ExtendableEntityInterface;
use Exception;
use Throwable;

class ObjectHelper
{
    private const GETTER_PREFIXES = ['get', 'is', 'has', ''];

    public static function getProperty(mixed $object, string $propertyName, $throwException = false): mixed
    {
        try {
            if (is_object($object) && ExtendableEntity::isExtendableEntity($object) && !property_exists($object, $propertyName)) {
                /** @var ExtendableEntityInterface $object */
                return $object->getCustomFieldsData($propertyName) ?? null;
            }

            if (is_array($object)) {
                return $object[$propertyName];
            }

            if (is_object($object)) {
                foreach (self::GETTER_PREFIXES as $prefix) {
                    $getter = $prefix . $propertyName;

                    if (method_exists($object, $getter)) {
                        return $object->{$getter}();
                    }
                }
            }

            throw new Exception('Getter method not found: ' . (implode('/', self::GETTER_PREFIXES)) . ' + ' . $propertyName);
        } catch (Throwable $exception) {
            if ($throwException) {
                throw $exception;
            }
        }

        return null;
    }

    public static function setProperty(mixed $object, string $propertyName, mixed $value, $throwException = false): mixed
    {
        try {
            if (is_array($object)) {
                $object[$propertyName] = $value;
            }

            if (is_object($object)) {
                $setter = 'set' . $propertyName;

                if (method_exists($object, $setter)) {
                    $object->{$setter}($value);
                }
            }
        } catch (Throwable $exception) {
            if ($throwException) {
                throw $exception;
            }
        }

        return $object;
    }
}
