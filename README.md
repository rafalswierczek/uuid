# Simple and fast UUID generator in PHP

## Installation:

> composer require rafalswierczek/uuid

## Requirements:
- PHP 8.3 with x64 architecture
- FFI (only for `Uuid4::createManyFfi`) https://www.php.net/manual/en/book.ffi.php

## Usage:

UUID v4:
```php
// basic usage:
$uuid4Static = Uuid4::create();
$uuid4New = new Uuid4('f3d7fa06-d938-4c22-9505-c585efa381df');
$valid = Uuid4::validate('f3d7fa06-d938-4c22-9505-c585efa381df');
$equals = $uuid4New->equals($uuid4Static);
$uuidList = Uuid4::createManyFfi(1000000);

// example: create random user id used by other services
$userExternalId = Uuid4::create();
```

UUID v5:
```php
// basic usage:
$uuid5Static = Uuid5::create(Uuid4::create(), 'seed');
$uuid5New = new Uuid5('0bfa18dd-3e8a-5810-b9b2-336b56af84b2');
$valid = Uuid5::validate('0bfa18dd-3e8a-5810-b9b2-336b56af84b2');
$equals = $uuid5New->equals($uuid5Static);

// example: get support tickets very fast based on query params
$standardNamespace = new Uuid1(Uuid5::NAMESPACE_DNS);
$yourAppNamespace = Uuid5::create($standardNamespace, 'internal.it-helpdesk.org');
$searchParams = json_encode([
    'from' => '2025-01-30 12:35',
    'department' => 'it',
    'type' => 'bug',
]);
$searchHash = Uuid5::create($yourAppNamespace, $searchParams);
$tickets = $cache->get($searchHash, function (ItemInterface $item) use ($searchParams): array {
    return getTickets($searchParams);
});
```

UUID v7:
```php
// basic usage:
$uuid7Static = Uuid7::create();
$uuid7New = new Uuid7('0199d59e-8041-7b74-b74e-b94310cd9473');
$valid = Uuid7::validate('0199d59e-8041-7b74-b74e-b94310cd9473');
$equals = $uuid7Static->equals($uuid7New);

// example: create indexable/monotonic list of events
$eventStream = [];
$eventStream[] = new UserCreatedEvent(id: Uuid7::create(), name: 'John');
$eventStream[] = new UserCreatedEvent(id: Uuid7::create(), name: 'Alice');
$eventStream[] = new UserCreatedEvent(id: Uuid7::create(), name: 'Bob');
$eventStream[] = new UserCreatedEvent(id: Uuid7::create(), name: 'Adam');
persistEvents($eventStream);
// let's say Alice id is 0199d59e-8041-7b74-b74e-b94310cd9473
$events = $this->query("SELECT * FROM event_log WHERE id > '0199d59e-8041-7b74-b74e-b94310cd9473'");
foreach ($events as $event) {
    echo $event->name.' ';
}
// Bob Adam
```

## Performance

#### UUID v4 generation performance:

| Library        | Amount   | Time       | Memory usage |
|----------------|----------|------------|--------------|
| rafalswierczek | 50000000 | 20.41 sec  | 7.09 GiB     |
| symfony        | 50000000 | 22.74 sec  | 8.95 GiB     |
| ramsey         | 50000000 | 43.59 sec  | 8.95 GiB     |

| Library        | Amount   | Time      | Memory usage |
|----------------|----------|-----------|--------------|
| rafalswierczek | 1000000  | 314.60 ms | 131.24 MiB   |
| symfony        | 1000000  | 328.46 ms | 169.39 MiB   |
| ramsey         | 1000000  | 805.66 ms | 169.39 MiB   |

| Library        | Amount   | Time      | Memory usage |
|----------------|----------|-----------|--------------|
| rafalswierczek | 1000     | 284.54 µs | 137.32 KiB   |
| symfony        | 1000     | 300.62 µs | 176.38 KiB   |
| ramsey         | 1000     | 716.17 µs | 176.38 KiB   |

| Library        | Amount   | Time    | Memory usage |
|----------------|----------|---------|--------------|
| rafalswierczek | 1        | 0.39 µs | 352.00 B     |
| symfony        | 1        | 0.35 µs | 376.00 B     |
| ramsey         | 1        | 0.80 µs | 376.00 B     |

_______________________________________________________________________

#### UUID v5 generation performance:

| Library        | Amount   | Time       | Memory usage |
|----------------|----------|------------|--------------|
| rafalswierczek | 50000000 | 45.28 sec  | 19.01 GiB    |
| symfony        | 50000000 | 36.57 sec  | 8.95 GiB     |
| ramsey         | 50000000 | 48.59 sec  | 8.95 GiB     |

| Library        | Amount   | Time      | Memory usage |
|----------------|----------|-----------|--------------|
| rafalswierczek | 1000000  | 589.02 ms | 375.38 MiB   |
| symfony        | 1000000  | 582.31 ms | 169.39 MiB   |
| ramsey         | 1000000  | 814.87 ms | 169.39 MiB   |

| Library        | Amount   | Time      | Memory usage |
|----------------|----------|-----------|--------------|
| rafalswierczek | 1000     | 548.62 µs | 387.32 KiB   |
| symfony        | 1000     | 550.67 µs | 176.38 KiB   |
| ramsey         | 1000     | 783.83 µs | 176.38 KiB   |

| Library        | Amount   | Time    | Memory usage |
|----------------|----------|---------|--------------|
| rafalswierczek | 1        | 0.60 µs | 592.00 B     |
| symfony        | 1        | 0.61 µs | 376.00 B     |
| ramsey         | 1        | 0.85 µs | 376.00 B     |

_______________________________________________________________________

#### UUID v7 generation performance:

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 50000000 | 41.50 sec    | 1295          | 19.01 GiB    |
| symfony        | 50000000 | 44.33 sec    | 1182          | 8.95 GiB     |
| ramsey         | 50000000 | 68.32 sec    | 755           | 8.95 GiB     |

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 1000000  | 515.38 ms    | 2006          | 375.38 MiB   |
| symfony        | 1000000  | 802.43 ms    | 1317          | 169.39 MiB   |
| ramsey         | 1000000  | 1.16 sec     | 875           | 169.39 MiB   |

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 1000     | 478.80 µs    | 770           | 387.32 KiB   |
| symfony        | 1000     | 678.17 µs    | 665           | 176.38 KiB   |
| ramsey         | 1000     | 1.12 ms      | 477           | 176.38 KiB   |

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 1        | 0.53 µs      | 1             | 592.00 B     |
| symfony        | 1        | 0.77 µs      | 1             | 376.00 B     |
| ramsey         | 1        | 1.23 µs      | 1             | 376.00 B     |


## Monotonicity of UUID v7

Implementation of UUID v7 in this library supports monotonicity (Method 2 [RFC 9562](https://www.rfc-editor.org/rfc/rfc9562.html#name-monotonicity-and-counters))

## Compilation of UUID v4 C code

Windows `ggcc -shared -O3 -o ../include/nwuuid4.dll uuid4_win_x64.c -lbcrypt`

Linux `gcc -shared -fPIC -O3 -s -o ../include/nwuuid4.so uuid4_linux_x64.c`

## Running performance tests

Each test case includes a CPU warm-up phase and a specific number of attempts, with average values provided as the result.
Additionally, I suggest running each test case 3 times to calculate an average of the averages for maximum precision.
Each test case should be executed separately, as this approach provides the most accurate results.

UUID v4:
```
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid4Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid4Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid4Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid4Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid4Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid4Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid4Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid4Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid4Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid4Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid4Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid4Ramsey"
```

UUID v5:
```
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid5Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid5Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid5Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid5Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid5Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid5Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid5Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid5Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid5Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid5Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid5Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid5Ramsey"
```

UUID v7:
```
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid7Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid7Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1Uuid7Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid7Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid7Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1KUuid7Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid7Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid7Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate1MUuid7Ramsey"

php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid7Rafalswierczek"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid7Symfony"
php -d memory_limit=32000M vendor/bin/phpunit --filter "testCreate50MUuid7Ramsey"
```