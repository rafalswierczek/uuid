# Simple and fast UUID generator in PHP

## Installation:

> composer require rafalswierczek/uuid

## Usage:

```php
// UUID v4:
$uuid4_1 = Uuid4::create();
$uuid4_2 = new Uuid4('f3d7fa06-d938-4c22-9505-c585efa381df');
$uuid4_equals = $uuid4_1->equals($uuid4_2);
$uuid4_valid = Uuid4::validate('f3d7fa06-d938-4c22-9505-c585efa381df');
$uuids = Uuid4::createManyFfi(1000000); // use this for batch generation

// UUID v7:
$uuid7_1 = Uuid7::create();
$uuid7_2 = new Uuid7('0199d59e-8041-7b74-b74e-b94310cd9473');
$uuid7_equals = $uuid7_1->equals($uuid7_2);
$uuid7_valid = Uuid7::validate('0199d59e-8041-7b74-b74e-b94310cd9473');
```

## Requirements:
- PHP 8.3 with x64 architecture
- FFI (only for `Uuid4::createManyFfi`) https://www.php.net/manual/en/book.ffi.php

## Performance

#### UUID v4 generation performance:

| Library        | Amount   | Time       | Memory usage |
|----------------|----------|------------|--------------|
| rafalswierczek | 50000000 | 38.87 sec  | 7.08 GiB     |
| symfony        | 50000000 | 53.98 sec  | 8.45 GiB     |
| ramsey         | 50000000 | 263.35 sec | 8.45 GiB     |

| Library        | Amount   | Time      | Memory usage |
|----------------|----------|-----------|--------------|
| rafalswierczek | 1000000  | 752.57 ms | 133.24 MiB   |
| symfony        | 1000000  | 989.38 ms | 170.59 MiB   |
| ramsey         | 1000000  | 5.21 sec  | 170.59 MiB   |

| Library        | Amount   | Time      | Memory usage |
|----------------|----------|-----------|--------------|
| rafalswierczek | 1000     | 738.16 µs | 137.32 KiB   |
| symfony        | 1000     | 972.50 µs | 176.30 KiB   |
| ramsey         | 1000     | 5.19 ms   | 176.30 KiB   |

| Library        | Amount   | Time    | Memory usage |
|----------------|----------|---------|--------------|
| rafalswierczek | 1        | 1.83 µs | 336.00 B     |
| symfony        | 1        | 1.33 µs | 376.00 B     |
| ramsey         | 1        | 5.62 µs | 376.00 B     |

_______________________________________________________________________

#### UUID v7 generation performance:

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 50000000 | 102.41 sec   | 547           | 18.51 GiB    |
| symfony        | 50000000 | 158.42 sec   | 324           | 8.45 GiB     |
| ramsey         | 50000000 | 420.62 sec   | 121           | 8.45 GiB     |

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 1000000  | 1.66 sec     | 605           | 376.58 MiB   |
| symfony        | 1000000  | 3.18 sec     | 317           | 170.59 MiB   |
| ramsey         | 1000000  | 8.16 sec     | 122           | 170.59 MiB   |

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 1000     | 1.61 ms      | 396           | 387.24 KiB   |
| symfony        | 1000     | 2.99 ms      | 255           | 176.30 KiB   |
| ramsey         | 1000     | 8.20 ms      | 110           | 176.30 KiB   |

| Library        | Amount   | Time seconds | Amount in 1ms | Memory usage |
|----------------|----------|--------------|---------------|--------------|
| rafalswierczek | 1        | 1.98 µs      | 1             | 592.00 B     |
| symfony        | 1        | 3.37 µs      | 1             | 376.00 B     |
| ramsey         | 1        | 8.66 µs      | 1             | 376.00 B     |

## Monotonicity of UUID v7

Implementation of UUID v7 in this library supports monotonicity (Method 2 [RFC 9562](https://www.rfc-editor.org/rfc/rfc9562.html#name-monotonicity-and-counters))

## Compilation of UUID v4 C code

Windows `ggcc -shared -O3 -o ../include/nwuuid4.dll uuid4_win_x64.c -lbcrypt`

Linux `gcc -shared -fPIC -O3 -s -o ../include/nwuuid4.so uuid4_linux_x64.c`
