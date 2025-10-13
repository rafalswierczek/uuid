# Super simple UUID generator in PHP

## Installation:

> composer require rafalswierczek/uuid

## Usage:

```php
// UUID v4:
$uuid4_1 = Uuid4::create();
$uuid4_2 = new Uuid4('f3d7fa06-d938-4c22-9505-c585efa381df');
$uuid4_equals = $uuid4_1->equals($uuid4_2);
$uuid4_valid = Uuid4::validate('f3d7fa06-d938-4c22-9505-c585efa381df');

// UUID v7:
$uuid7_1 = Uuid7::create();
$uuid7_2 = new Uuid7('0199d59e-8041-7b74-b74e-b94310cd9473');
$uuid7_equals = $uuid7_1->equals($uuid7_2);
$uuid7_valid = Uuid7::validate('0199d59e-8041-7b74-b74e-b94310cd9473');
```

## Requirements:
PHP 8.3 with x64 architecture for UUID v7

## Performance

### UUID v4

>More than 2 times faster than ramsey/uuid

UUID v4 Generation Performance: **rafalswierczek/uuid** vs **ramsey/uuid**:

| Library        | Amount   | Time       | Memory usage  |
|----------------|----------|------------|---------------|
| rafalswierczek | 10000000 | 23.66 sec  | 0.0000381 MiB |
| ramsey         | 10000000 | 51.52 sec  | 0.5805206 MiB |
| rafalswierczek | 1000000  | 2.36 sec   | 0.0003052 MiB |
| ramsey         | 1000000  | 5.15 sec   | 0.0003052 MiB |
| rafalswierczek | 1000     | 2.40 ms    | 0.0003052 MiB |
| ramsey         | 1000     | 5.26 ms    | 0.0003052 MiB |
| rafalswierczek | 1        | 0.00906 ms | 0.0003052 MiB |
| ramsey         | 1        | 0.01192 ms | 0.0003052 MiB |

### UUID v7

>More than 2 times faster than ramsey/uuid

UUID v7 Generation Performance: **rafalswierczek/uuid** vs **ramsey/uuid**:

| Library        | Amount   | Time          | Amount 1ms | Memory usage  |
|----------------|----------|---------------|------------|---------------|
| rafalswierczek | 10000000 | 31.04 sec     | 325        | 3971.8077 MiB |
| ramsey         | 10000000 | 99.15 sec     | 101        | 1783.8800 MiB |
| rafalswierczek | 1000000  | 3.05 sec      | 330        | 376.58192 MiB |
| ramsey         | 1000000  | 8.18 sec      | 122        | 170.58827 MiB |
| rafalswierczek | 1000     | 2.97 ms       | 250        | 0.3784714 MiB |
| ramsey         | 1000     | 7.98 ms       | 111        | 0.1724777 MiB |
| rafalswierczek | 1        | 0.00001215 ms | 1          | 0.0008698 MiB |
| ramsey         | 1        | 0.00002384 ms | 1          | 0.0006638 MiB |


## Monotonicity of UUID v7

Implementation of UUID v7 in this library supports monotonicity (Method 2 [RFC 9562](https://www.rfc-editor.org/rfc/rfc9562.html#name-monotonicity-and-counters))
