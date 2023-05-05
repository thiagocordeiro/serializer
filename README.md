# PHP Serializer

This package allows you to parse json objects into php classes without the overhead of annotations, it checks for php object constructor and creates cache classes to convert json into a given class.

## How to use
For Symfony projects a bundle is available at [`serializer-bundle`](https://github.com/thiagocordeiro/serializer-bundle)

For Laravel projects a package is available at [`laravel-serializer`](https://github.com/thiagocordeiro/laravel-serializer) 

otherwise the package is available on composer:
```
composer require thiagocordeiro/serializer
```

PHP Serializer does not use setters, so your class must have a constructor with all properties coming from the json.

#### Basic example (Using getters)
```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

class User
{
    private string $name;
    private string $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

```

#### Basic example (Using readonly properties)
```php
class User
{
    public readonly string $name;
    public readonly string $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
}
```

Once you have your class, you can convert json string to it, ex.

```php
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\JsonSerializer;

$encoder = new EncoderFactory(PipelineEncoderFileLoader::full('path/to/cache'));
$decoder = new DecoderFactory(PipelineDecoderFileLoader::full('path/to/cache'));
$serializer = new JsonSerializer($encoder, $decoder);

$json = '{"name":"Arthur Dent","email":"arthur.dent@galaxy.org"}';
$serializer->deserialize($json, \App\ValueObject\User::class);

// or

$json = '[
  {"name":"Arthur Dent","email":"arthur.dent@galaxy.org"},
  {"name":"Chuck Norris","email":"chuck@norrs.com"},
  ...
]';
$serializer->deserialize($json, \App\ValueObject\User::class);


```
The opposite way, ex.

```php
use Serializer\Builder\Encoder\EncoderFactory;
use Serializer\Builder\Encoder\FileLoader\PipelineEncoderFileLoader;
use Serializer\Builder\Decoder\DecoderFactory;
use Serializer\Builder\Decoder\FileLoader\PipelineDecoderFileLoader;
use Serializer\JsonSerializer;

$encoder = new EncoderFactory(PipelineEncoderFileLoader::full('path/to/cache'));
$decoder = new DecoderFactory(PipelineDecoderFileLoader::full('path/to/cache'));
$serializer = new JsonSerializer($encoder, $decoder);

$user = new \App\ValueObject\User('Arthur Dent', 'arthur.dent@galaxy.org');

$json = $serializer->serialize($user);
// will return {"name":"Arthur Dent","email":"arthur.dent@galaxy.org"}

// or
$json = $serializer->serialize([$user1, $user2, ...]);
```
---
#### Complex object construction - Objects
Constructors can also have arrays and other classes on it , the other class must follow same rules, for ex.
```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

class User
{
    // properties...

    public function __construct(string $name, string $email, ?Address $address)
    {
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
    }

    // getters
}
```
for this example a json string could contain or not `address` property:
```json
{
    "name": "Arthur Dent",
    "email": "arthur.dent@galaxy.org"
}
```
or
```json
{
    "name": "Arthur Dent",
    "email": "arthur.dent@galaxy.org",
    "address": {
        "street": "Times Square",
        "contry": "USA"
    }
}
```
#### Complex object construction - Arrays
Arrays are also welcome, but since there is no way to determine on the array type on object constructor, arrays require you to write php annotation, a simple doc bloc is everything it needs to deserialize any json string, ex

```php
<?php

use ValueObject\Address;

class User
{
    // properties...

    /**
     * @param Address[] $addresses
     */
    public function __construct(string $name, array $addresses)
    {
        $this->name = $name;
        $this->addresses = $addresses;
    }

    // getters
}
```
A json for this class would be:
```json
{
    "name": "Arthur Dent",
    "addresses": [
        {"street":"Times Square", "contry": "USA"},
        {"street":"Leidseplein", "contry": "Netherlands"}
    ]
}
```
---

#### Default values
By default all **non-provided** values will be filled with `null`, if the property is not nullable then you a `TypeError` will be thrown, but a property can also have a default value and in case it is not given, the default value will be used, ex.
```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

class User
{
    // properties...

    public function __construct(string $name, string $email, string $type = 'user')
    {
        $this->name = $name;
        $this->email = $email;
        $this->type = $type;
    }

    // getters
}
```

A json without `type` would result an object with `$type = 'user'`.
Note: Default value will only be used in case it is not provided, if it is provided as `null` then `null` will be the value.

#### Constructor with arguments
```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

class User
{
    // properties...

    public function __construct(Place ...$places)
    {
        $this->places = $places;
    }

    // getters
}
```

#### Constructor with DateTime/DateTimeImmutable
```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

class User
{
    // properties...

    public function __construct(DateTime $createdAt, DateTimeImmutable $updatedAt)
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // getters
}
```

## Contributing
Feel free to open issues or submit PRs

### Supporting
If you feel like supporting changes then you can send donations to the address below.

Bitcoin Address: bc1qfyudlcxqnvqzxxgpvsfmadwudg4znk2z3asj9h
