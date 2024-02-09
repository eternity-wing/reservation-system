<?php

namespace ReservationSystem\Domain\Events;

use ReservationSystem\Domain\ValueObjects\Schedule;

class AppointmentIsScheduled implements DomainEventsInterface
{
    /**
     * @param Schedule $schedule
     */
    public function __construct(private readonly Schedule $schedule)
    {
    }
}