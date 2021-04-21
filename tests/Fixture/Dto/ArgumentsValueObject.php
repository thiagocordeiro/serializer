<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class ArgumentsValueObject
{
    /** @var Place[] */
    private array $places;

    private User $user;

    public function __construct(User $user, Place ...$places)
    {
        $this->places = $places;
        $this->user = $user;
    }

    /**
     * @return Place[]
     */
    public function getPlaces(): array
    {
        return $this->places;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
