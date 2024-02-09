<?php

namespace ReservationSystem\Domain\ValueObjects;

use ReservationSystem\Domain\Exception\DomainException;

final class Guid
{
    /**
     * @param string $value
     * @throws DomainException
     */
    public function __construct(public readonly string $value){
        if (!preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $value)) {
            throw new DomainException("invalid guid");
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}