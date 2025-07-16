[![Packagist Version](https://img.shields.io/packagist/v/masyasmv/freedom-broker-api?style=flat-square)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/masyasmv/freedom-broker-api?style=flat-square)](https://php.net)
[![Total Downloads](https://poser.pugx.org/masyasmv/freedom-broker-api/downloads)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![Tests](https://github.com/MasyaSmv/freedom-broker-api/actions/workflows/workflow.yml/badge.svg)](https://github.com/MasyaSmv/freedom-broker-api/actions)
[![Coverage Status](https://coveralls.io/repos/github/MasyaSmv/freedom-broker-api/badge.svg?branch=main)](https://coveralls.io/github/MasyaSmv/freedom-broker-api?branch=main)
[![License](https://img.shields.io/packagist/l/masyasmv/freedom-broker-api?style=flat-square)](LICENSE)

# Freedom Broker API SDK for PHP 8 / Laravel 8 +

Lean, fully‐typed SDK for **Freedom24 (TraderNet) broker API**:

| Domain          | What you get                                                                                                   |
|-----------------|----------------------------------------------------------------------------------------------------------------|
| **Reports**     | JSON fetch → strict DTO collections<br>balances, operations, positions, _payments_ (dividends / compensations) |
| **Trading**     | send, cancel, list orders                                                                                      |
| **Market data** | securities catalog, quotes, historical bars                                                                    |
| **Auth**        | V1 & V2 HMAC request signing                                                                                   |
| **Tooling**     | `ReportPeriodDTO` (start/end helpers)                                                                          |

> All DTOs are Psalm-/PHPStan-friendly; persistence/business logic stays in **your**
> app.([GitHub](https://raw.githubusercontent.com/MasyaSmv/freedom-broker-api/develop/README.md))

---

## ✨ What’s new (v 1.3.0)

* `PaymentDTO` + `payments` collection in parser (dividends, tax compensation).
* Safe numeric casting: `"-"` or `""` ⇒ `0.0` in money fields.
* `ReportPeriodDTO` with helpers `contains()` & `lengthInDays()`.
* 100 % unit-coverage on GitHub Actions & Coveralls.
* README overhaul 😎

Full list: see [CHANGELOG](CHANGELOG.md).

---

## ️Requirements

* PHP **≥ 8.0** (8.1–8.3 preferred)
* `ext-json`, `ext-mbstring`
* Guzzle 7+
* Laravel 8/9/10 (auto-discover)

---

## Installation

```bash
composer require masyasmv/freedom-broker-api
```

### Laravel setup

```bash
php artisan vendor:publish \
  --provider="MasyaSmv\FreedomBrokerApi\Laravel\Providers\FreedomBrokerServiceProvider" \
  --tag="freedom-config"
```

```dotenv
FREEDOM_PUBLIC_KEY=your_public_key
FREEDOM_PRIVATE_KEY=your_secret_key
```

That’s it – the package facade **`Freedom::`** is ready.

---

## Quick Start

```php
use MasyaSmv\FreedomBrokerApi\Laravel\Facades\Freedom;

$public  = config('freedom.public_key');
$private = config('freedom.private_key');
// 1. Full broker report – now includes `payments`
$report = Freedom::load($public, $private, '2025-06-01', '2025-06-30');
$dividends = $report['payments'];      // Collection<PaymentDTO>

// 2. Send order
$order = Freedom::orders()->send('AAPL.US', 1, 182.50, 'ACC-123');

// 3. Quote info
$quote = Freedom::quotes()->get('AAPL.US');

// 4. Historical bars
$bars = Freedom::history()
        ->history('AAPL.US', new DateTime('2024-01-01'), new DateTime());
```

---

## API Reference (brief)

| Service                               | Facade/helper        | Returns / DTO                 |
|---------------------------------------|----------------------|-------------------------------|
| `load($from,$to)`                     | `Freedom::load()`    | `array{…}` – see above        |
| `StockService::all()`                 | `Freedom::stocks()`  | `Collection<StockDTO>`        |
| `QuoteInfoService::get($ticker)`      | `Freedom::quotes()`  | `StockDTO                     |null` |
| `StockHistoryService::history()`      | `Freedom::history()` | `Collection<StockHistoryDTO>` |
| `OrderService::send/cancel/history()` | `Freedom::orders()`  | `OrderDTO                     |CancelOrderDTO|Collection` |

---

## ✅ Quality

```bash
composer test       # PHPUnit (unit + stub integration)
composer coverage   # html/text coverage
composer cs         # style check
composer cs-fix     # auto-fix
composer phpstan    # static analysis (level 8)
```

Live API tests (`FREEDOM_LIVE_TEST=1`) are opt-in to keep CI
fast.([GitHub](https://raw.githubusercontent.com/MasyaSmv/freedom-broker-api/develop/composer.json))

---

## Contributing

1. Fork → feature branch (`feat/xxx`)
2. Add tests (aim ≥ 95 % cov.)
3. `composer test && composer cs`
4. PR against `develop`

We follow PSR-12 + Laravel conventions.

---

## License

Released under the **MIT** license.
© Masya Smv, 2025
