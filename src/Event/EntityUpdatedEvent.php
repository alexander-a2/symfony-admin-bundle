<?php

namespace AlexanderA2\AdminBundle\Event;

class EntityUpdatedEvent
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
