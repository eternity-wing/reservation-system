<?php

namespace ReservationSystem\Infrastructure\Repository;

use PDO;
use PDOException;
use ReservationSystem\Domain\Entity\Appointment;
use ReservationSystem\Domain\Exception\DomainException;
use ReservationSystem\Domain\Repository\AppointmentRepositoryInterface;
use ReservationSystem\Domain\ValueObjects\Guid;
use ReservationSystem\Domain\ValueObjects\Schedule;
use Throwable;

class SQLAppointmentRepository implements AppointmentRepositoryInterface
{
    public const TABLE_NAME = "appointments";
    private const INTEGRITY_CONSTRAINT_VIOLATION = "23000";
    private readonly PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::make();
    }

    /**
     * @param Appointment $appointment
     * @return void
     * @throws DomainException
     * @throws Throwable
     */
    public function save(Appointment $appointment): void
    {
        try {
            $params = [];
            foreach ($appointment->mappedData() as $key => $value){
                $params[":{$key}"] = $value;
            }
            $sql = "INSERT INTO " . self::TABLE_NAME . "(".implode(",", array_keys($appointment->mappedData())) .")". "VALUES(". implode(", ", array_keys($params)) .")";

            $statement = $this->pdo->prepare($sql);

            $statement->execute($params);
        } catch (PDOException $throwable){
            if ($throwable->getCode() === self::INTEGRITY_CONSTRAINT_VIOLATION) {
                throw new DomainException("already existed", self::INTEGRITY_CONSTRAINT_VIOLATION);
            }
            throw $throwable;
        }
    }


    /**
     * @return Appointment[]
     * @throws DomainException
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE_NAME . " ORDER BY schedule ASC";
        $statement = $this->pdo->query($sql);
        return array_map(static function(array $rawData): Appointment{
            return Appointment::fromMappedData($rawData);
        },
            $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param Guid $userId
     * @param Schedule $schedule
     * @return Appointment|null
     * @throws DomainException
     */
    public function find(Guid $userId, Schedule $schedule): ?Appointment
    {
        $statement = $this->pdo->prepare("SELECT * FROM ". self::TABLE_NAME ." WHERE schedule= :schedule AND user_id = :userId LIMIT 1");

        $statement->execute([':schedule' => $schedule->toString(), ':userId' => $userId->value]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row ? Appointment::fromMappedData($row) : null;
    }

    /**
     * @param Appointment $appointment
     * @return void
     */
    public function delete(Appointment $appointment): void
    {
        $statement = $this->pdo->prepare("DELETE FROM ". self::TABLE_NAME ." WHERE schedule = :schedule");
        $statement->execute([':schedule' => $appointment->schedule->toString()]);
    }
}