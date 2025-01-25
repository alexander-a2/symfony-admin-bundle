<?php

namespace AlexanderA2\AdminBundle\Datasheet;

use AlexanderA2\AdminBundle\Datasheet\DataType\EmptyDataType;
use AlexanderA2\AdminBundle\Helper\ObjectHelper;

class DatasheetColumn implements DatasheetColumnInterface
{
    protected string $title;

    protected ?int $width = null;

    protected mixed $handler = null;

    protected ?string $align = null;

    public function __construct(
        protected string $name,
        protected ?string $dataType,
    ) {
        $this->title = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDataType(): string
    {
        return $this->dataType;
    }

    public function setDataType(?string $dataType): self
    {
        $this->dataType = $dataType;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHandler(): mixed
    {
        return $this->handler;
    }

    public function setHandler($handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function getContent(mixed $record): string
    {
        $value = ObjectHelper::getProperty($record, $this->name) ?? null;

        if ($this->handler) {
            return call_user_func($this->handler, $value, $record, $this);
        }

        if (EmptyDataType::is($value)) {
            return EmptyDataType::toFormatted($value);
        }

        return call_user_func_array([$this->getDataType(), 'toFormatted'], [$value]);
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }

    public function setAlign(?string $align): self
    {
        $this->align = $align;

        return $this;
    }

    public function getStyles(): string
    {
        $styles = [];

        if (!empty($this->getAlign())) {
            $styles[] = 'text-align: ' . $this->getAlign();
        }

        if (!empty($this->getWidth())) {
            $styles[] = 'width: ' . $this->getWidth() . 'px';
        }

        return implode('; ', $styles);
    }
}
