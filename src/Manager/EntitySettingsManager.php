<?php

namespace AlexanderA2\AdminBundle\Manager;

use AlexanderA2\AdminBundle\Component\EntitySettings;
use AlexanderA2\AdminBundle\Event\EntitySettingsBuildEvent;
use AlexanderA2\AdminBundle\Helper\EntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EntitySettingsManager
{
    protected ?EntitySettings $entitySettings = null;

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected EntityManagerInterface $entityManager,
        protected EntityHelper $entityHelper,
    ) {
    }

    public function getFromContext(): ?EntitySettings
    {
        return $this->entitySettings;
    }

    public function setContextEntity(mixed $subject): void
    {
        $entityFqcn = null;
        $entityId = null;
        $entityObject = null;

        if (is_string($subject) && class_exists($subject)) {
            $entityFqcn = $subject;
        } elseif (is_array($subject)) {
            $entityFqcn = $subject[0];
            $entityId = $subject[1];
        } elseif (is_object($subject)) {
            $entityFqcn = get_class($subject);
            $entityId = $subject->getId();
            $entityObject = $subject;
        }
        $entitySettings = new EntitySettings($entityFqcn, $entityId);

        if ($entitySettings->isSingleView()) {
            $entitySettings->setPageTitle($this->buildPageTitle($entitySettings, $entityObject));
        }else{
            $entitySettings->setPageTitle($entitySettings->getName()); // pluralize?
        }
        $this->eventDispatcher->dispatch(new EntitySettingsBuildEvent($entitySettings));
        $this->entitySettings = $entitySettings;
    }

    protected function buildPageTitle(EntitySettings $entitySettings, mixed $entityObject): string
    {
        $primaryFieldName = EntityHelper::guessPrimaryFieldName(
            $this->entityHelper->getEntityFields($entitySettings->getFqcn())
        );

        if ($primaryFieldName !== 'id') {
            if (empty($entityObject)) {
                $entityObject = $this->entityManager->getRepository(
                    $entitySettings->getFqcn())->find($entitySettings->getId()
                );
            }
            $objectName = $entityObject->{'get' . ucfirst($primaryFieldName)}();

            return sprintf('%s «%s»', $entitySettings->getName(), $objectName);
        }

        if (method_exists($entityObject, '__toString')) {
            return $entityObject->__toString();
        }

        return sprintf('%s #%s', $entitySettings->getName(), $entitySettings->getId());
    }
}
