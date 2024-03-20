<?php

namespace AlexanderA2\SymfonyAdminBundle\Twig;

use AlexanderA2\SymfonyAdminBundle\Builder\MenuBuilder;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\TwigRenderer;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminPanelExtension extends AbstractExtension
{
    public function __construct(
        protected MenuBuilder $menuBuilder,
        protected Environment $twig,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu', [$this, 'getMenu'], ['is_safe' => ['html']]),
            new TwigFunction('menuItems', [$this, 'getMenuItems'], ['is_safe' => ['html']]),
        ];
    }

    public function getMenu(string $name): string
    {
//        $renderer = new ListRenderer(new Matcher());
        $renderer = new TwigRenderer($this->twig, '@Admin/menu.html.twig', new Matcher());

        return $renderer->render(
            $this->menuBuilder->build($name),
        );
    }

    public function getMenuItems(string $name): array
    {
        return $this->menuBuilder->build($name)->getChildren();
    }
}