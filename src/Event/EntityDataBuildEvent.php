<?php

namespace AlexanderA2\SymfonyAdminBundle\Event;

class EntityDataBuildEvent
{
    public function __construct(
        protected object $subject,
        protected array $data,
    ){
    }

    public function getSubject(): object
    {
        return $this->subject;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }
}