<?php

namespace Test\Serializer\Unit\Param;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Serializer\Param\RuntimeParamSpecification;
use Serializer\Param\RuntimeParamSpecificationRepository;
use Test\Serializer\Fixture\Dto\ReadOnly\Place;
use Test\Serializer\Fixture\Dto\ReadOnly\Trip\Trip;
use Test\Serializer\Fixture\Dto\ReadOnly\User;
use Test\Serializer\Fixture\Dto\TripStatus;

class ReflectionClassParamResolverTest extends TestCase
{
    #[Test] public function given_a_class_Then_get_its_param_specifications(): void
    {
        $resolver = new RuntimeParamSpecificationRepository();

        $params = $resolver->of(Trip::class);

        $this->assertEquals(
            [
                'driver' => new RuntimeParamSpecification(
                    name: 'driver',
                    type: User::class,
                    isList: false,
                    default: null,
                ),
                'stops' => new RuntimeParamSpecification(
                    name: 'stops',
                    type: Place::class,
                    isList: true,
                    default: null,
                ),
                'status' => new RuntimeParamSpecification(
                    name: 'status',
                    type: TripStatus::class,
                    isList: true,
                    default: null,
                ),
                'remarks' => new RuntimeParamSpecification(
                    name: 'remarks',
                    type: 'string',
                    isList: true,
                    default: [],
                ),
                'description' => new RuntimeParamSpecification(
                    name: 'description',
                    type: 'string',
                    isList: false,
                    default: '',
                ),
            ],
            $params,
        );
    }
}
