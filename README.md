[![Packagist Version](https://img.shields.io/packagist/v/masyasmv/freedom-broker-api?style=flat-square)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/masyasmv/freedom-broker-api?style=flat-square)](https://php.net)
[![Total Downloads](https://poser.pugx.org/masyasmv/freedom-broker-api/downloads)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![Tests](https://github.com/MasyaSmv/freedom-broker-api/actions/workflows/workflow.yml/badge.svg)](https://github.com/MasyaSmv/freedom-broker-api/actions)
[![Coverage Status](https://coveralls.io/repos/github/MasyaSmv/freedom-broker-api/badge.svg?branch=main)](https://coveralls.io/github/MasyaSmv/freedom-broker-api?branch=main)
[![License](https://img.shields.io/packagist/l/masyasmv/freedom-broker-api?style=flat-square)](LICENSE)

# Freedom Broker API SDK

Typed PHP SDK for Freedom24 / TraderNet broker API.

The package keeps broker credentials outside package config. Pass a public/private
key pair when opening a session, or provide credentials through your own runtime
provider.

## Features

| Domain | API |
| --- | --- |
| Reports | Broker report loading and typed DTO collections for account, operations, commissions, payments, positions, balances and summary |
| Account | Credential validation through `getOPQ`, raw response and typed `AccountProfileDTO` |
| Securities | Securities list with normalized `ApiResponse` and typed `StockDTO` collection |
| Market data | Quote info and quote history |
| Transport | V1/V2 signing, normalized response metadata, payload and API errors |
| Laravel | Facade, service provider, runtime sessions and backward-compatible `load()` |

## Requirements

- PHP 8.0+
- `ext-json`, `ext-mbstring`
- Guzzle 7+
- Laravel 8/9/10 for Laravel auto-discovery

## Installation

```bash
composer require masyasmv/freedom-broker-api
```

For Laravel, publish package config:

```bash
php artisan vendor:publish \
  --provider="MasyaSmv\FreedomBrokerApi\Laravel\Providers\FreedomBrokerServiceProvider" \
  --tag="freedom-config"
```

Only transport defaults live in config:

```dotenv
FREEDOM_API_URL=https://tradernet.ru/api
FREEDOM_API_VERSION=2
FREEDOM_HTTP_TIMEOUT=15
FREEDOM_HTTP_CONNECT_TIMEOUT=10
FREEDOM_HTTP_RETRIES=0
FREEDOM_HTTP_RETRY_DELAY=100
FREEDOM_HTTP_VERIFY_SSL=true
FREEDOM_HTTP_USER_AGENT=freedom-broker-api
```

Do not store user broker keys in `config/freedom.php`. Pass them at runtime.

## Laravel Usage

```php
use MasyaSmv\FreedomBrokerApi\Laravel\Facades\Freedom;
use DateTimeImmutable;

$session = Freedom::connect(
    publicKey: $user->freedom_public_key,
    privateKey: $user->freedom_private_key,
);

$profile = $session->accountProfile(); // AccountProfileDTO
$report = $session->loadBrokerReport('2025-06-01', '2025-06-30');
$stocks = $session->securitiesList(take: 100);
$quote = $session->quoteInfo('AAPL.US');
$bars = $session->quoteHistory('AAPL.US', new DateTimeImmutable('2024-01-01'), new DateTimeImmutable());
```

`Freedom::load($publicKey, $privateKey, $from, $to)` still works for existing
integrations and returns the broker report array.

## Pure PHP Usage

```php
use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Core\Factory\FreedomClientFactory;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\FreedomSession;

$config = new FreedomConfig(version: 2);
$factory = new FreedomClientFactory($config);
$credentials = new FreedomCredentials($publicKey, $privateKey);

$session = new FreedomSession(
    client: $factory->make($credentials),
    apiClient: $factory->makeApiClient($credentials),
);

$response = $session->validateCredentials(); // ApiResponse
$profile = $session->accountProfile();      // AccountProfileDTO
```

Use `CredentialsProviderInterface` when keys are resolved lazily from a vault,
database row or request context:

```php
$session = Freedom::withCredentials($provider);
```

## Normalized Responses

Low-level methods return `ApiResponse`:

```php
$response = $session->quoteInfoResponse('AAPL.US');

if (!$response->successful()) {
    $error = $response->error();
}

$payload = $response->payloadOrFail();
$data = $payload->data();
```

Typed session methods map normalized responses into DTOs and collections:

| Method | Return |
| --- | --- |
| `accountProfile()` | `AccountProfileDTO` |
| `loadBrokerReport()` | broker report array with typed collections |
| `securitiesList()` | `Collection<int, StockDTO>` |
| `quoteInfo()` | `?StockDTO` |
| `quoteHistory()` | `Collection<int, StockHistoryDTO>` |

## Compatibility Notes

`Freedom::load()` is kept for the current migration window. New code should
prefer `Freedom::connect()` and typed methods on `FreedomSession`.

See [Migration Guide](docs/MIGRATION-FOUNDATION.md) for the current refactor
notes.

## Quality

```bash
composer test
composer coverage
composer cs
composer cs-fix
vendor/bin/phpstan analyse src tests
```

Live API tests are opt-in:

```bash
FREEDOM_LIVE_TEST=1 vendor/bin/phpunit --filter FreedomHttpClientTest
```

## License

Released under the MIT license.
