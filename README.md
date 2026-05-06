[![Packagist Version](https://img.shields.io/packagist/v/masyasmv/freedom-broker-api?style=flat-square)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/masyasmv/freedom-broker-api?style=flat-square)](https://php.net)
[![Total Downloads](https://poser.pugx.org/masyasmv/freedom-broker-api/downloads)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![Tests](https://github.com/MasyaSmv/freedom-broker-api/actions/workflows/workflow.yml/badge.svg)](https://github.com/MasyaSmv/freedom-broker-api/actions)
[![Coverage Status](https://coveralls.io/repos/github/MasyaSmv/freedom-broker-api/badge.svg?branch=main)](https://coveralls.io/github/MasyaSmv/freedom-broker-api?branch=main)
[![License](https://img.shields.io/packagist/l/masyasmv/freedom-broker-api?style=flat-square)](LICENSE)

# Freedom Broker API SDK

Typed PHP SDK for the Freedom24 / TraderNet broker API (Tradernet). Covers all documented
HTTP endpoints.

Credentials stay outside the package config. Pass a public/private key pair at session
creation, or wire up your own `CredentialsProviderInterface` for lazy resolution from a
vault, database row, or request context.

## Features

| Domain | Endpoints / capabilities |
|--------|--------------------------|
| **Account** | Credential validation (`getOPQ`), typed `AccountProfileDTO` |
| **Reports** | Broker report with typed DTO collections; depositary report (raw); cashflows (`getUserCashFlows`); direct download URL builders |
| **Orders** | Active orders, order history; place (`putTradeOrder`), cancel (`delTradeOrder`), stop-loss / take-profit (`putStopLoss`) |
| **Instructions** | Client instruction history (`getClientCpsHistory`), instruction files (`getCpsFiles`) |
| **Portfolio** | Portfolio snapshot (`getPositionJson`) |
| **Currency** | Cross rates on date (`getCrossRatesForDate`) |
| **Market — Securities** | Securities list (`getAllSecurities`) with typed `StockDTO` collection |
| **Market — Quotes** | Quote info (`getSecurityInfo`), HLOC history (`getHloc`), multi-ticker quotes (`getStockQuotesJson`) |
| **Market — Trades** | Live trades, trades history (`getTradesHistory`) |
| **Market — Discovery** | Market status (`getMarketStatus`), ticker search (`tickerFinder`), news (`getNews`), top securities, check allowed ticker, options by market |
| **Watchlists** | Full CRUD — create, update, delete lists; add/remove tickers; select active list |
| **Price Alerts** | List (`getAlertsList`), add, delete (`togglePriceAlert`) |
| **Security Sessions** | List active security sessions (`getSecuritySessions`) |
| **Reference Data** | Static lookup classes: `InstrumentType`, `TradingPlatform`, `OrderStatus`, `PassportType`, `ReceptionOffice`, `SignatureType`, `TransactionTypeCode`, `CpsDocType`, `CurrencyCode` |
| **Transport** | V1/V2 signing, `ApiResponse` / `ApiPayload` / `ApiError` / `ApiMeta`, retry logic, request context |
| **Laravel** | Facade, service provider, runtime sessions, backward-compatible `load()` |

## Requirements

- PHP 8.0+
- `ext-json`, `ext-mbstring`
- Guzzle 7+
- Laravel 8/9/10/11 for Laravel auto-discovery

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

Only transport defaults live in config. Do not store broker keys there:

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

## Laravel Usage

```php
use MasyaSmv\FreedomBrokerApi\Laravel\Facades\Freedom;
use DateTimeImmutable;

$session = Freedom::connect(
    publicKey: $user->freedom_public_key,
    privateKey: $user->freedom_private_key,
);

// Account
$profile   = $session->accountProfile();                  // AccountProfileDTO

// Reports
$report    = $session->loadBrokerReport('2025-01-01', '2025-12-31');
$flows     = $session->cashFlows('ACC-001', '2025-01-01', '2025-12-31');
$depUrl    = \MasyaSmv\FreedomBrokerApi\Core\ReportLinkBuilder::depositaryReportUrl(
    'ACC-001', '2025-01-01', '2025-12-31', $token
);

// Portfolio
$portfolio = $session->portfolioInfo();                   // array

// Market data
$stocks    = $session->securitiesList(take: 100);         // Collection<StockDTO>
$quote     = $session->quoteInfo('AAPL.US');              // ?StockDTO
$bars      = $session->quoteHistory(
    'AAPL.US', new DateTimeImmutable('2024-01-01'), new DateTimeImmutable()
);                                                        // Collection<StockHistoryDTO>
$status    = $session->marketStatus('US');                // Collection<MarketStatusDTO>
$found     = $session->tickerFinder('Apple');             // Collection<TickerSearchResultDTO>
$top       = $session->topSecurities('US', 'stocks');     // Collection<int, string>

// Orders
$history   = $session->orderHistory('2025-01-01', '2025-12-31');
$current   = $session->currentOrders();
$placed    = $session->placeOrder(...);                   // PlacedOrderDTO
$cancelled = $session->cancelOrder($orderId);             // CancelledOrderDTO
$sl        = $session->setStopLoss(...);                  // StopLossResultDTO

// Instructions
$cpsHistory = $session->clientInstructionHistory('ACC-001', '2025-01-01', '2025-12-31');
$files      = $session->cpsFiles();

// Currency
$rates     = $session->crossRates('USD', ['RUB', 'EUR']); // CrossRatesDTO

// Watchlists
$lists     = $session->watchlists();                      // Collection<WatchlistDTO>
$session->addWatchlist('My Tech', tickers: ['AAPL.US', 'MSFT.US']);

// Price Alerts
$alerts    = $session->priceAlerts('AAPL.US');            // Collection<PriceAlertDTO>

// Security Sessions
$sessions  = $session->securitySessions();               // Collection<SecuritySessionDTO>
```

`Freedom::load($publicKey, $privateKey, $from, $to)` still works for existing integrations
and returns the broker report array unchanged.

## Pure PHP Usage

```php
use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Core\Factory\FreedomClientFactory;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\FreedomSession;

$config  = new FreedomConfig(version: 2);
$factory = new FreedomClientFactory($config);
$creds   = new FreedomCredentials($publicKey, $privateKey);

$session = new FreedomSession(
    client:    $factory->make($creds),
    apiClient: $factory->makeApiClient($creds),
);

$response = $session->validateCredentials(); // ApiResponse
$profile  = $session->accountProfile();      // AccountProfileDTO
```

Use `CredentialsProviderInterface` when keys are resolved lazily:

```php
$session = Freedom::withCredentials($provider);
```

## Session Methods Reference

Every typed method has a `*Response()` counterpart returning `ApiResponse` directly.

### Account

| Method | Return type |
|--------|-------------|
| `validateCredentials()` | `ApiResponse` |
| `accountProfile()` | `AccountProfileDTO` |

### Reports

| Method | Return type |
|--------|-------------|
| `loadBrokerReport(from, to)` | `array` (compatibility) |
| `loadBrokerReportResponse(from, to)` | `ApiResponse` |
| `depositaryReportResponse(...)` | `ApiResponse` |
| `cashFlows(account, from, to, ...)` | `Collection<CashFlowDTO>` |
| `ReportLinkBuilder::brokerReportUrl(...)` | `string` |
| `ReportLinkBuilder::depositaryReportUrl(...)` | `string` |

### Orders

| Method | Return type |
|--------|-------------|
| `orderHistory(from, till)` | `Collection<OrderDTO>` |
| `currentOrders(?activeOnly)` | `Collection<OrderDTO>` |
| `placeOrder(...)` | `PlacedOrderDTO` |
| `cancelOrder(orderId)` | `CancelledOrderDTO` |
| `setStopLoss(...)` | `StopLossResultDTO` |

### Instructions

| Method | Return type |
|--------|-------------|
| `clientInstructionHistory(account, from, to)` | `Collection<ClientInstructionDTO>` |
| `cpsFiles(?id, ?internalId)` | `Collection<CpsFileDTO>` |

### Portfolio & Currency

| Method | Return type |
|--------|-------------|
| `portfolioInfo()` | `array` |
| `crossRates(base, currencies, ?date)` | `CrossRatesDTO` |

### Market — Securities & Quotes

| Method | Return type |
|--------|-------------|
| `securitiesList(take, skip)` | `Collection<StockDTO>` |
| `quoteInfo(ticker)` | `?StockDTO` |
| `quoteHistory(ticker, from, to)` | `Collection<StockHistoryDTO>` |
| `stockQuotes(tickers[])` | `Collection<StockQuoteDTO>` |

### Market — Trades

| Method | Return type |
|--------|-------------|
| `trades(ticker)` | `Collection<TradesDTO>` |
| `tradesHistory(ticker, from, to, ...)` | `Collection<TradeHistoryDTO>` |

### Market — Discovery

| Method | Return type |
|--------|-------------|
| `marketStatus(market, ?mode)` | `Collection<MarketStatusDTO>` |
| `tickerFinder(text)` | `Collection<TickerSearchResultDTO>` |
| `newsResponse(ticker, ...)` | `ApiResponse` (raw only) |
| `topSecurities(market, type, ...)` | `Collection<int, string>` |
| `allowedTicker(ticker, ?checkBan)` | `AllowedTickerDTO` |
| `optionsByMarket(market, baseContractCode)` | `Collection<OptionContractDTO>` |

### Watchlists

| Method | Return type |
|--------|-------------|
| `watchlists()` | `Collection<WatchlistDTO>` |
| `addWatchlist(name, ?picture, tickers[])` | `Collection<WatchlistDTO>` |
| `updateWatchlist(id, name, picture, index)` | `Collection<WatchlistDTO>` |
| `deleteWatchlist(id)` | `Collection<WatchlistDTO>` |
| `addWatchlistTicker(id, ticker, index)` | `Collection<WatchlistDTO>` |
| `deleteWatchlistTicker(id, ticker)` | `Collection<WatchlistDTO>` |
| `selectWatchlist(id)` | `Collection<WatchlistDTO>` |

### Price Alerts

| Method | Return type |
|--------|-------------|
| `priceAlerts(?ticker)` | `Collection<PriceAlertDTO>` |
| `addPriceAlertResponse(...)` | `ApiResponse` |
| `deletePriceAlertResponse(id, ...)` | `ApiResponse` |

### Security Sessions

| Method | Return type |
|--------|-------------|
| `securitySessions()` | `Collection<SecuritySessionDTO>` |

## Reference Data

Static lookup classes in `MasyaSmv\FreedomBrokerApi\Reference`:

```php
use MasyaSmv\FreedomBrokerApi\Reference\InstrumentType;
use MasyaSmv\FreedomBrokerApi\Reference\CurrencyCode;
use MasyaSmv\FreedomBrokerApi\Reference\TransactionTypeCode;

InstrumentType::all();           // ['stock' => 'Stock', ...]
InstrumentType::label('bond');   // 'Bond'
CurrencyCode::label(840);        // 'USD'
TransactionTypeCode::label(101); // code description
```

| Class | Description | Values |
|-------|-------------|--------|
| `InstrumentType` | Security instrument types | 17 |
| `TradingPlatform` | Trading platforms / exchanges | 54 |
| `OrderStatus` | Order status codes | 15 |
| `PassportType` | Document / passport types | 32 |
| `ReceptionOffice` | Reception office types | 3 |
| `SignatureType` | Signature types | 18 |
| `TransactionTypeCode` | Transaction type codes | 85 |
| `CpsDocType` | CPS instruction document types | 150+ |
| `CurrencyCode` | ISO currency codes | 60 |

## Normalized Responses

Every endpoint has a `*Response()` method returning `ApiResponse`:

```php
$response = $session->quoteInfoResponse('AAPL.US');

if (!$response->successful()) {
    $error   = $response->error();   // ApiError
    $context = $response->context(); // ApiRequestContext
}

$payload = $response->payloadOrFail(); // ApiPayload
$data    = $payload->data();
$meta    = $payload->meta();           // ApiMeta
```

## Out of Scope

| Feature | Reason |
|---------|--------|
| `authLogin`, `authBySms`, `getAuthSms`, `getSidInfo` | SID-based auth not needed for API key SDK |
| `getSecuritySessionCode`, `checkSecuritySessionCode` | Require SID-based 2FA flow |
| `orderBook` | Websocket-only, no HTTP endpoint |
| Websocket / realtime subscriptions | Separate module, not mixed with HTTP layer |

## Compatibility Notes

`Freedom::load()` and `FreedomManager::load()` work unchanged for existing integrations.
New code should prefer `Freedom::connect()` and typed session methods.

See [Migration Guide](docs/MIGRATION-FOUNDATION.md) for details.

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
