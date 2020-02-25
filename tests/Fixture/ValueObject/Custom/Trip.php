<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\ValueObject\Custom;

class Trip
{
    /** @var string */
    private $from;

    /** @var string */
    private $to;

    /** @var string */
    private $type;

    /** @var Vehicle */
    private $vehicle;

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
