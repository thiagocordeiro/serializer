<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

use DateTimeImmutable;

class SearchQuery
{
    private ?string $customer;
    private ?DateTimeImmutable $creationDate;
    private int $limit;

    public function __construct(?string $customer, ?DateTimeImmutable $creationDate, int $limit = 3)
    {
        $this->customer = $customer;
        $this->creationDate = $creationDate;
        $this->limit = $limit;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function getCreationDate(): ?DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
