<?php

namespace ReservationSystem\Domain\Services;

use ReservationSystem\Domain\Events\DomainEventsInterface;

interface EventDispatcherInterface
{
    /**
     * @param DomainEventsInterface[] $events
     * @return void
     */
    public function dispatchAll(array $events):void;
}