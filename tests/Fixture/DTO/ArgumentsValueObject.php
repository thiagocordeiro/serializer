<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\DTO;

class ArgumentsValueObject
{
    /** @var Place[] */
    private $places;

    /** @var User */
    private $user;

    public function __construct(User $user, Place ...$places)
    {
        $this->places = $places;
        $this->user = $user;
    }

    public function getPlaces(): array
    {
        return $this->places;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
