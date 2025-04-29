<?php

namespace AlexanderA2\AdminBundle\Event;

class EntityCreatedEvent
{
    public function __construct(
        protected object $subject,
    ) {
    }

    public function getSubject(): object
    {
        return $this->subject;
    }
}
