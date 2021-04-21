<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\Custom;

class Airplane implements Vehicle
{
    private string $airline;
    private string $aircraft;
    private string $registration;
    private int $maxPassengers;

    public function __construct(string $airline, string $aircraft, string $registration, int $maxPassengers)
    {
        $this->airline = $airline;
        $this->aircraft = $aircraft;
        $this->registration = $registration;
        $this->maxPassengers = $maxPassengers;
    }

    public function getAirline(): string
    {
        return $this->airline;
    }

    public function getAircraft(): string
    {
        return $this->aircraft;
    }

    public function getRegistration(): string
    {
        return $this->registration;
    }

    public function getMaxPassengers(): int
    {
        return $this->maxPassengers;
    }
}
