<?php

namespace AlexanderA2\AdminBundle\Datasheet;

class DatasheetColumnCustomized extends DatasheetColumn
{
    protected array $customizedAttributes = [];

    public function setDataType(?string $dataType): DatasheetColumn
    {
        $this->customizedAttributes[] = 'dataType';

        return parent::setDataType($dataType);
    }

    public function setHandler($handler): self
    {
        $this->customizedAttributes[] = 'handler';

        return parent::setHandler($handler);
    }

    public function setTitle(string $title): self
    {
        $this->customizedAttributes[] = 'title';

        return parent::setTitle($title);
    }

    public function setWidth(?int $width): self
    {
        $this->customizedAttributes[] = 'width';

        return parent::setWidth($width);
    }

    public function setAlign(?string $align): self
    {
        $this->customizedAttributes[] = 'align';

        return parent::setAlign($align);
    }

    public function getCustomizedAttributes(): array
    {
        return $this->customizedAttributes;
    }
}
