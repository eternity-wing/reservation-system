<?php

namespace ReservationSystem\Domain\Repository;

use ReservationSystem\Domain\Entity\Appointment;
use ReservationSystem\Domain\ValueObjects\Guid;
use ReservationSystem\Domain\ValueObjects\Schedule;

interface AppointmentRepositoryInterface
{
    /**
     * @param Appointment $appointment
     * @return void
     */
    public function save(Appointment $appointment): void;

    /**
     * @param Guid $userId
     * @param Schedule $schedule
     * @return Appointment|null
     */
    public function find(Guid $userId, Schedule $schedule): ?Appointment;

    /**
     * @param Appointment $appointment
     * @return void
     */
    public function delete(Appointment $appointment): void;

    /**
     * @return Appointment[]
     */
    public function findAll(): array;
}