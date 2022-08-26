<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\Collection;

use ArrayIterator;
use IteratorAggregate;
use Test\Serializer\Fixture\Dto\Place;
use Traversable;

class PlaceIterableCollection implements IteratorAggregate
{
    /** @var iterable<Place> */
    private iterable $places;

    public function __construct(Place ...$places)
    {
        $this->places = new ArrayIterator($places);
    }

    /**
     * @return Traversable<Place>
     */
    public function getIterator(): Traversable
    {
        return $this->places;
    }
}
