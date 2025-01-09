<?php

namespace AlexanderA2\AdminBundle\Datasheet;

interface DatasheetColumnInterface
{
    public function __construct(
        string $name,
        string $dataType,
    );

    public function getName(): string;

    public function getDataType(): string;

    public function setDataType(?string $dataType): self;

    public function getTitle(): string;

    public function setTitle(string $title): self;

    public function getWidth(): ?int;

    public function setWidth(?int $width): self;

    public function getHandler(): mixed;

    public function setHandler($handler): self;

    public function getContent(mixed $record): string;
}
