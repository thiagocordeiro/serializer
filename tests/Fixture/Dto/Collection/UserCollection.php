<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\Collection;

use Test\Serializer\Fixture\Dto\User;

class UserCollection
{
    /** @var User[] */
    private array $users;

    /**
     * @param User[] $users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
