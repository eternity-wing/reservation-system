<?php

namespace ReservationSystem\Application\Handlers;

use ReservationSystem\Domain\Entity\Appointment;
use ReservationSystem\Domain\Exception\DomainException;
use ReservationSystem\Domain\Repository\AppointmentRepositoryInterface;
use ReservationSystem\Domain\Services\EventDispatcherInterface;
use ReservationSystem\Domain\ValueObjects\Guid;
use ReservationSystem\Domain\ValueObjects\Schedule;

final class AppointmentService
{
    /**
     * @param AppointmentRepositoryInterface $appointmentRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
        private readonly EventDispatcherInterface $eventDispatcher)
    {}

    /**
     * @throws DomainException
     */
    public function scheduleAppointment(string $userId, \DateTime $schedule):void{
        $appointment = Appointment::schedule(
            new Guid($userId),
            new \DateTime('now'),
            new Schedule($schedule)
        );

        $this->appointmentRepository->save($appointment);

        $this->eventDispatcher->dispatchAll($appointment->domainEvents());
    }
    /**
     * @throws DomainException
     */
    public function cancelAppointment(string $userId, \DateTime $schedule):void{
        $appointment = $this->appointmentRepository->find(new Guid($userId), new Schedule($schedule));
        if (!$appointment) {
            throw new DomainException('not found', 404);
        }
        $appointment->cancel();
        $this->appointmentRepository->delete($appointment);

        $this->eventDispatcher->dispatchAll($appointment->domainEvents());
    }

    /**
     * @return array
     */
    public function appointments():array{
        $result = [];

        foreach ($this->appointmentRepository->findAll() as $appointment){
            if(!isset($result[$appointment->schedule->dateAsString()])){
                $result[$appointment->schedule->dateAsString()] = [];
            }
            $result[$appointment->schedule->dateAsString()][] = $appointment->mappedData();
        }

        return $result;
    }
}