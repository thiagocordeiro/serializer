<?php

namespace Tcds\Io\Serializer\Unit\Param;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Serializer\Fixture\AccountStatus;
use Tcds\Io\Serializer\Fixture\ReadOnly\AccountHolder;
use Tcds\Io\Serializer\Fixture\ReadOnly\Address;
use Tcds\Io\Serializer\Fixture\ReadOnly\BankAccount;
use Tcds\Io\Serializer\Fixture\ReadOnly\Place;
use Tcds\Io\Serializer\Fixture\ReadOnly\Trip\Trip;
use Tcds\Io\Serializer\Fixture\ReadOnly\Trip\TripStatus;
use Tcds\Io\Serializer\Fixture\ReadOnly\User;
use Tcds\Io\Serializer\Metadata\Generic;
use Tcds\Io\Serializer\Param\RuntimeParamSpecification;
use Tcds\Io\Serializer\Param\RuntimeParamSpecificationRepository;

class RuntimeParamSpecificationRepositoryTest extends TestCase
{
    #[Test] public function trip(): void
    {
        $resolver = new RuntimeParamSpecificationRepository();

        $params = $resolver->of(Trip::class);

        $this->assertEquals(
            [
                'driver' => new RuntimeParamSpecification(
                    name: 'driver',
                    type: new Generic(User::class, null, []),
                    default: null,
                ),
                'stops' => new RuntimeParamSpecification(
                    name: 'stops',
                    type: new Generic('list', 'Place[]', [Place::class]),
                    default: null,
                ),
                'status' => new RuntimeParamSpecification(
                    name: 'status',
                    type: new Generic('list', 'TripStatus[]', [TripStatus::class]),
                    default: null,
                ),
                'remarks' => new RuntimeParamSpecification(
                    name: 'remarks',
                    type: new Generic('list', 'string[]', ['string']),
                    default: [],
                ),
                'description' => new RuntimeParamSpecification(
                    name: 'description',
                    type: new Generic('string', null, []),
                    default: '',
                ),
            ],
            $params,
        );
    }

    #[Test] public function account_holder(): void
    {
        $resolver = new RuntimeParamSpecificationRepository();

        $params = $resolver->of(AccountHolder::class);

        $this->assertEquals(
            [
                'name' => new RuntimeParamSpecification(
                    name: 'name',
                    type: new Generic('string', null, []),
                    default: null,
                ),
                'account' => new RuntimeParamSpecification(
                    name: 'account',
                    type: new Generic(BankAccount::class, null, []),
                    default: null,
                ),
                'status' => new RuntimeParamSpecification(
                    name: 'status',
                    type: new Generic('list', 'AccountStatus[]', [AccountStatus::class]),
                    default: null,
                ),
                'address' => new RuntimeParamSpecification(
                    name: 'address',
                    type: new Generic(Address::class, null, []),
                    default: null,
                ),
            ],
            $params,
        );
    }
}
