<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto;

class AnyBool
{
    private bool $active;
    private bool $blocked;
    private bool $restrictions;

    public function __construct(bool $active, bool $blocked, bool $restrictions)
    {
        $this->active = $active;
        $this->blocked = $blocked;
        $this->restrictions = $restrictions;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function wasBlocked(): bool
    {
        return $this->blocked;
    }

    public function hasRestrictions(): bool
    {
        return $this->restrictions;
    }
}
