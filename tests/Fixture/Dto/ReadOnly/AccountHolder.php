<?php

declare(strict_types=1);

namespace Test\Serializer\Fixture\Dto\ReadOnly;

use Test\Serializer\Fixture\Dto\AccountStatus;
use Test\Serializer\Fixture\Dto\AccountType;

readonly class AccountHolder
{
    /**
     * @param AccountStatus[] $status
     */
    public function __construct(
        public string $name,
        public BankAccount $account,
        public Address $address,
        public array $status,
    ) {
    }

    public static function thiagoCordeiro(): self
    {
        return new self(
            name: 'Thiago Cordeiro',
            account: new BankAccount(
                number: '12345-X',
                type: AccountType::CHECKING,
            ),
            address: new Address(
                street: 'street street',
                number: 100,
                main: false,
                place: new Place(
                    city: 'São Paulo',
                    country: 'Brazil',
                    position: new LatLng(
                        lat: -26.9013,
                        lng: -48.6655,
                    ),
                ),
            ),
            status: [
                AccountStatus::ACTIVE,
                AccountStatus::FINALISED,
            ],
        );
    }

    public static function data(): array
    {
        return [
            'name' => 'Thiago Cordeiro',
            'account' => [
                'number' => '12345-X',
                'type' => 'checking',
            ],
            'active' => 'true',
            'address' => [
                'street' => 'street street',
                'number' => '100',
                'main' => 'false',
                'place' => [
                    'city' => 'São Paulo',
                    'country' => 'Brazil',
                    'position' => [
                        'lat' => '-26.9013',
                        'lng' => '-48.6655',
                    ],
                ],
            ],
            'status' => [
                'Active',
                'Finalized',
            ],
        ];
    }

    public static function json(): string
    {
        return <<<JSON
        {
          "name": "Thiago Cordeiro",
          "account": {
            "number": "12345-X",
            "type": "checking"
          },
          "active": "true",
          "address": {
            "street": "street street",
            "number": "100",
            "main": "false",
            "place": {
              "city": "São Paulo",
              "country": "Brazil",
              "position": {
                "lat": "-26.9013",
                "lng": "-48.6655"
              }
            }
          },
          "status": [
            "Active",
            "Finalized"
          ]
        }
        JSON;
    }

    public static function partialJsonValue(): string
    {
        return <<<JSON
        {
          "active": "true",
          "address": {
            "street": "street street",
            "number": "100",
            "main": "false",
            "place": {
              "city": "São Paulo",
              "country": "Brazil",
              "position": {
                "lat": "-26.9013",
                "lng": "-48.6655"
              }
            }
          },
          "status": [
            "Active",
            "Finalized"
          ]
        }
        JSON;
    }
}
