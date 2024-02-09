<?php

namespace ReservationSystem\Domain\Entity;

use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use ReservationSystem\Domain\Events\AppointmentIsCancelled;
use ReservationSystem\Domain\Events\AppointmentIsScheduled;
use ReservationSystem\Domain\Events\DomainEventsInterface;
use ReservationSystem\Domain\Exception\DomainException;
use ReservationSystem\Domain\ValueObjects\Guid;
use ReservationSystem\Domain\ValueObjects\Schedule;

class Appointment
{
    private const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';
    /**
     * @var DomainEventsInterface[]
     */
    private array $domainEvents;

    /**
     * @param Guid     $userId
     * @param DateTime $createdAt
     * @param Schedule $schedule
     */
    public function __construct(
        public readonly Guid          $userId,
        public readonly DateTime      $createdAt,
        public readonly Schedule      $schedule
    ){
    }

    /**
     * @param array $data
     * @return Appointment
     * @throws DomainException
     */
    public static function fromMappedData(array $data): Appointment{
        return new self(
            new Guid($data["user_id"]),
            DateTime::createFromFormat(self::DEFAULT_DATETIME_FORMAT, $data["created_at"]),
            new Schedule(DateTime::createFromFormat(self::DEFAULT_DATETIME_FORMAT, $data["schedule"]))
        );
    }

    /**
     * @param Guid $userId
     * @param DateTime $createdAt
     * @param Schedule $schedule
     * @return Appointment
     */
    public static function schedule(
        Guid          $userId,
        DateTime      $createdAt,
        Schedule      $schedule
    ): Appointment{
        $appointment = new self($userId, $createdAt, $schedule);
        $appointment->domainEvents[] = new AppointmentIsScheduled($appointment->schedule);

        return $appointment;
    }
    /**
     * @return void
     */
    public function cancel(): void{
        $this->domainEvents[] = new AppointmentIsCancelled($this->schedule);
    }

    /**
     * @return DomainEventsInterface[]
     */
    public function domainEvents(): array{
        return $this->domainEvents;
    }

    /**
     * @return array
     */
    #[ArrayShape(['user_id' => Guid::class, 'created_at' => DateTime::class, 'schedule' => 'string'])]
    public function mappedData(): array{
        return [
            'user_id' => $this->userId,
            'created_at' => $this->createdAt->format(self::DEFAULT_DATETIME_FORMAT),
            'schedule' => $this->schedule->toString(),
        ];
    }
}