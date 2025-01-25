<?php

namespace AlexanderA2\AdminBundle\Twig;

use AlexanderA2\AdminBundle\Component\EntitySettings;
use AlexanderA2\AdminBundle\Manager\EntitySettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminRenderExtension extends AbstractExtension
{
    public function __construct(
        protected EntitySettingsManager $entitySettingsProvider,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('set_entity_context', [$this, 'setEntityContext']),
            new TwigFunction('entity_settings', [$this, 'getEntitySettings']),
        ];
    }

    public function setEntityContext(mixed $subject): void
    {
        $this->entitySettingsProvider->setContextEntity($subject);
    }

    public function getEntitySettings(): EntitySettings
    {
        return $this->entitySettingsProvider->getFromContext();
    }
}
