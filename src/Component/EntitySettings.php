<?php

namespace AlexanderA2\AdminBundle\Component;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;

class EntitySettings
{
    protected ?ItemInterface $tabs = null;

    protected ?ItemInterface $menu = null;

    protected ?string $pageTitle = null;

    public function __construct(
        protected string $fqcn,
        protected ?int $id = null,
    ) {
    }

    public function getMenu(): ItemInterface
    {
        if (empty($this->menu)) {
            $this->menu = (new MenuFactory())->createItem('adminEntityMenu');
        }

        return $this->menu;
    }

    public function setMenu(?ItemInterface $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function getTabs(): ItemInterface
    {
        if (empty($this->tabs)) {
            $this->tabs = (new MenuFactory())->createItem('adminEntityTabs');
        }

        return $this->tabs;
    }

    public function setTabs(?ItemInterface $tabs): self
    {
        $this->tabs = $tabs;

        return $this;
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return (new \ReflectionClass($this->fqcn))->getShortName();
    }

    public function isSingleView(): bool
    {
        return !empty($this->id);
    }

    public function isMultipleView(): bool
    {
        return empty($this->id);
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(?string $pageTitle): void
    {
        $this->pageTitle = $pageTitle;
    }
}
