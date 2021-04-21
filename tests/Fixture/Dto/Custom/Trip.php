<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\Custom;

class Trip
{
    private string $from;
    private string $to;
    private string $type;
    private Vehicle $vehicle;

    public function __construct(string $from, string $to, string $type, Vehicle $vehicle)
    {
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;
        $this->vehicle = $vehicle;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }
}
