<?php

namespace ReservationSystem\Domain\ValueObjects;

use ReservationSystem\Domain\Exception\DomainException;

final class Schedule
{
    private const FIRST_SCHEDULE = "07:00";
    private const LAST_SCHEDULE = "14:30";
    public const DEFAULT_FORMAT = "Y-m-d H:i";

    public readonly \DateTime $value;

    /**
     * @throws DomainException
     */
    public function __construct(\DateTime $value)
    {
        $minuteString = $value->format('i');
        if ($minuteString !== "00" && $minuteString !== "30") {
            throw new DomainException("invalid schedule time");
        }
        if ($value->format('H:i') < self::FIRST_SCHEDULE || $value->format('H:i') > self::LAST_SCHEDULE) {
            throw new DomainException("invalid schedule time");
        }
        if (self::isWeekend($value)) {
            throw new DomainException("weekend!");
        }
        $this->value = \DateTime::createFromFormat(self::DEFAULT_FORMAT, $value->format(self::DEFAULT_FORMAT));
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    private static function isWeekend(\DateTime $dateTime): bool{
        return (int) $dateTime->format('N') === 5;
    }

    /**
     * @param string|null $format
     * @return string
     */
    public function toString(?string $format = self::DEFAULT_FORMAT): string
    {
        return $this->value->format($format);
    }
    /**
     * @param string|null $format
     * @return string
     */
    public function dateAsString(): string
    {
        return $this->value->format('Y-m-d');
    }
}