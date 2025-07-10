[![Latest Stable Version](https://poser.pugx.org/masyasmv/freedom-broker-api/v/stable)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![Total Downloads](https://poser.pugx.org/masyasmv/freedom-broker-api/downloads)](https://packagist.org/packages/masyasmv/freedom-broker-api)
[![Build Status](https://github.com/MasyaSmv/freedom-broker-api/actions/workflows/workflow.yml/badge.svg)](https://github.com/MasyaSmv/freedom-broker-api/actions)
[![Coverage Status](https://coveralls.io/repos/github/MasyaSmv/freedom-broker-api/badge.svg?branch=main)](https://coveralls.io/github/MasyaSmv/freedom-broker-api?branch=main)
[![License](https://poser.pugx.org/masyasmv/freedom-broker-api/license)](LICENSE)

# Freedom Broker API SDK for PHP

**Freedom Broker (TraderNet) API** client — a lean, PSR-4, PHP 8.0+ and Laravel 8 library for:

- Authentication & Request Signing (V1, V2 cases)
- Fetching & parsing **broker reports**
- Retrieving **securities catalog**, **quotes info** and **historical data**
- Sending, cancelling and listing **orders**

All data comes back as strict **DTO**s and **Collections**; you handle persistence and business logic in your app.

---

## 🛠️ Requirements

- PHP ≥ 8.0
- ext-json, ext-mbstring
- Guzzle HTTP client (will be pulled in as dependency)
- illuminate/collections (via `orchestra/testbench` dev dependency)

---

## 🚀 Installation

```bash
composer require masyasmv/freedom-broker-api
```

### Laravel Integration

Laravel 8+ will auto-discover the package’s ServiceProvider & Facade.

1. **Publish config**
   ```bash
   php artisan vendor:publish      --provider="MasyaSmv\FreedomBrokerApi\Laravel\Providers\FreedomBrokerServiceProvider"      --tag="freedom-config"
   ```
2. **Set your API keys** in `.env`:
   ```dotenv
   FREEDOM_PUBLIC_KEY=your_public_api_key
   FREEDOM_PRIVATE_KEY=your_secret_api_key
   ```

---

## 📖 Usage Examples

### 1. Fetch & Parse Broker Report

```php
use MasyaSmv\FreedomBrokerApi\Laravel\Facades\Freedom;

/** @var array{
 *     plain:\MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO,
 *     operations:\Illuminate\Support\Collection,
 *     positions:\Illuminate\Support\Collection,
 *     balances:\Illuminate\Support\Collection
 * } $report */
\$report = Freedom::load('2025-01-01', '2025-01-31');
```

### 2. Securities Catalog

```php
use MasyaSmv\FreedomBrokerApi\Core\Service\StockService;

\$stocks = app(StockService::class)->all(); // Collection<StockDTO>
```

### 3. Quote Info

```php
use MasyaSmv\FreedomBrokerApi\Core\Service\QuoteInfoService;

\$info = app(QuoteInfoService::class)->get('AAPL.US'); // StockDTO|null
```

### 4. Historical Prices

```php
use MasyaSmv\FreedomBrokerApi\Core\Service\StockHistoryService;

\$history = app(StockHistoryService::class)
    ->history('AAPL.US', new DateTime('2020-01-01'), new DateTime('now'));
```

### 5. Orders

```php
use MasyaSmv\FreedomBrokerApi\Core\Service\OrderService;

\$order   = app(OrderService::class)
    ->send('AAPL.US', 1.0, 150.00, 'ACC-123');
\$cancel  = app(OrderService::class)->cancel(\$order->orderId);
\$history = app(OrderService::class)
    ->history('ACC-123', '2025-01-01', '2025-02-01');
```

---

## ✅ Testing & Quality

```bash
composer test        # run PHPUnit unit + live tests
composer live-test   # only live tests (pings real API)
composer coverage    # run with coverage report
composer cs          # check PSR-12 coding style
composer cs-fix      # auto-fix style issues
```

Live tests are **disabled by default**; to enable all of them:

```bash
FREEDOM_LIVE_TEST=1 composer test
```

---

## 🙌 Contributing

1. Fork & clone
2. Create a feature branch
3. Write unit tests (`tests/…`)
4. Run `composer test`
5. Send a PR

Please follow [PSR-12] style, add PHPDoc for new methods, and aim for 100 % coverage.

---

## ❤️ Support & Sponsorship

If you find this package useful, please ⭐️ the repo and consider sponsoring development.

---

## 📄 License

This library is released under the **MIT License**. See [LICENSE](LICENSE) for details.
