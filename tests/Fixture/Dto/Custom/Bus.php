<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\Custom;

class Bus implements Vehicle
{
    private string $company;
    private string $model;
    private int $maxPassengers;

    public function __construct(string $company, string $model, int $maxPassengers)
    {
        $this->company = $company;
        $this->model = $model;
        $this->maxPassengers = $maxPassengers;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getMaxPassengers(): int
    {
        return $this->maxPassengers;
    }
}
