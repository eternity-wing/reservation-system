<?php

namespace ReservationSystem\Application\Services;

use ReservationSystem\Domain\Events\DomainEventsInterface;
use ReservationSystem\Domain\Services\EventDispatcherInterface;

final class FakeEventDispatcher implements EventDispatcherInterface
{
    /**
     * @param DomainEventsInterface[] $events
     * @return void
     */
    public function dispatchAll(array $events): void
    {
        // TODO: Implement dispatchAll() method.
    }
}