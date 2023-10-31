<?php

namespace AlexanderA2\SymfonyAdminBundle\Twig;

use AlexanderA2\SymfonyAdminBundle\AdminBundle;
use AlexanderA2\SymfonyAdminBundle\Builder\MenuBuilder;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Renderer\ListRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminPanelExtension extends AbstractExtension
{
    public function __construct(
        protected MenuBuilder $menuBuilder,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getAdminPanelMainMenu', [$this, 'renderAdminPanelMainMenu'], ['is_safe' => ['html']]),
        ];
    }

    public function renderAdminPanelMainMenu(): string
    {
        $renderer = new ListRenderer(new Matcher());

        return $renderer->render(
            $this->menuBuilder->build(AdminBundle::MAIN_MENU_NAME),
        );
    }
}