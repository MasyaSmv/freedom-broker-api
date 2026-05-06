# Migration Guide: SDK Foundation Refactor

This guide covers the current foundation refactor on `sdk-foundation-refactor`.

## Credentials

Before, integrations commonly read broker keys from package config:

```php
$public = config('freedom.public_key');
$private = config('freedom.private_key');
$report = Freedom::load($public, $private, $from, $to);
```

Now package config stores only API URL, API version and HTTP options. Broker keys
should come from the current user/account context:

```php
$session = Freedom::connect($account->public_key, $account->private_key);
$report = $session->loadBrokerReport($from, $to);
```

`Freedom::load()` remains available as a compatibility wrapper, but new code
should use `FreedomSession`.

## Account Validation

Use the typed path when checking credentials:

```php
$profile = Freedom::connect($publicKey, $privateKey)->accountProfile();

if ($profile->clientCode === null) {
    // Keep the raw payload for fields TraderNet returns under account-specific keys.
    $raw = $profile->raw;
}
```

For diagnostics or custom mapping, use the raw normalized response:

```php
$response = Freedom::connect($publicKey, $privateKey)->validateCredentials();
$payload = $response->payloadOrFail()->data();
```

## Reports

The broker report shape is unchanged at the high level:

```php
$report = $session->loadBrokerReport('2025-06-01', '2025-06-30');

$plain = $report['plain'];
$operations = $report['operations'];
$payments = $report['payments'];
$summary = $report['summary'];
```

Internally, the new path goes through normalized `ApiResponse` and `ReportMapper`
when the session is built with `FreedomClientFactory::makeApiClient()`.

## Market And Securities

Prefer typed session methods:

```php
$stocks = $session->securitiesList(take: 100);
$quote = $session->quoteInfo('AAPL.US');
$history = $session->quoteHistory('AAPL.US', $from, $to);
```

Use response methods when an integration needs metadata, raw body or API error
details:

```php
$response = $session->quoteInfoResponse('AAPL.US');
$statusCode = $response->meta()->statusCode();
$rawBody = $response->rawBody();
```

## Orders

Read-only order history is available through the new session:

```php
$orders = $session->orderHistory('ACC-001', '2025-01-01', '2025-01-31');
```

Order status and trading commands still need a normalized resource layer. The
old `Core\Service\OrderService` remains in the package for send/cancel/history
compatibility during this phase.

## Suggested Whiteswan Migration Order

1. Replace config-based broker keys with runtime user/account credentials.
2. Switch credential validation to `Freedom::connect(...)->accountProfile()`.
3. Keep report loading on `loadBrokerReport()` and compare persisted report
   output with the previous `Freedom::load()` wrapper.
4. Move securities and market lookups to typed session methods.
5. Move read-only order history to `orderHistory()`; keep send/cancel on the
   compatibility service until the trading block is refactored.
