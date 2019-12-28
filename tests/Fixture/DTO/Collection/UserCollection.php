<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\DTO\Collection;

use Test\Serializer\Fixture\DTO\User;

class UserCollection
{
    /** @var User[] */
    private $users;

    /**
     * @param User[] $users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}
