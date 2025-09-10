<?php

namespace Test\Serializer\Unit;

use PHPUnit\Framework\Attributes\Test;
use Serializer\JsonObjectMapper;
use Serializer\Reader\Reader;
use Serializer\Reader\RuntimeReader;
use Test\Serializer\Fixture\Dto\ReadOnly\AccountHolder;
use Test\Serializer\SerializerTestCase;

class JsonObjectMapperTest extends SerializerTestCase
{
    private object $object;
    private string $json;
    private string $partialJson;

    private Reader $reader;

    protected function setUp(): void
    {
        $this->object = AccountHolder::thiagoCordeiro();
        $this->json = AccountHolder::json();
        $this->partialJson = AccountHolder::partialJsonValue();

        $this->reader = new RuntimeReader();
    }

    #[Test] public function given_a_json_then_parse_into_the_object(): void
    {
        $mapper = new JsonObjectMapper($this->reader, []);

        $accountHolder = $mapper->readJsonValue(AccountHolder::class, $this->json);

        $this->assertEquals($this->object, $accountHolder);
    }

    #[Test] public function given_a_json_then_parse_into_the_object_with_additional_content(): void
    {
        $mapper = new JsonObjectMapper($this->reader, []);

        $accountHolder = $mapper->readJsonValue(AccountHolder::class, $this->partialJson, [
            'name' => 'Thiago Cordeiro',
            'account' => [
                'number' => '12345-X',
                'type' => 'checking',
            ],
        ]);

        $this->assertEquals($this->object, $accountHolder);
    }
}
