<?php

namespace MasyaSmv\FreedomBrokerApi\Laravel\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Contracts\FreedomManagerInterface;
use MasyaSmv\FreedomBrokerApi\Core\Factory\FreedomClientFactory;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\SignerFactory;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Retry\RetryPolicy;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\Laravel\FreedomManager;
use Psr\Log\LoggerInterface;

class FreedomBrokerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FreedomConfig::class, function () {
            return FreedomConfig::fromArray((array)config('freedom', []));
        });

        $this->app->bind(ClientInterface::class, function () {
            /** @var FreedomConfig $config */
            $config = $this->app->make(FreedomConfig::class);

            return new Client([
                'timeout' => $config->http()->timeout(),
                'connect_timeout' => $config->http()->connectTimeout(),
                'verify' => $config->http()->verifySsl(),
                'headers' => [
                    'User-Agent' => $config->http()->userAgent(),
                ],
            ]);
        });

        $this->app->singleton(SignerFactory::class, SignerFactory::class);

        $this->app->singleton(FreedomClientFactory::class, function ($app) {
            return new FreedomClientFactory(
                $app->make(FreedomConfig::class),
                $app->make(SignerFactory::class),
                $app->make(ClientInterface::class),
            );
        });

        // Deprecated compatibility bind. New code should use FreedomManager::client/connect().
        $this->app->bind(FreedomHttpClient::class, function ($app) {
            return $app->make(FreedomClientFactory::class)->make(
                new FreedomCredentials(
                    (string)config('freedom.public_key', ''),
                    (string)config('freedom.private_key', ''),
                )
            );
        });

        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService(
                $app->make(FreedomHttpClient::class),
                new ReportParser(),
            );
        });

        $this->app->singleton('freedom', function ($app) {
            $logger = null;
            $channel = (string)config('freedom.log_channel', '');
            if ($channel !== '' && function_exists('logger')) {
                $logger = logger()->channel($channel);
            } elseif ($app->bound(LoggerInterface::class)) {
                $logger = $app->make(LoggerInterface::class);
            }

            $retry = $app->bound(RetryPolicy::class)
                ? $app->make(RetryPolicy::class)
                : new RetryPolicy(
                    maxAttempts: (int)config('freedom.retry.max_attempts', 3),
                    initialSleepSeconds: (int)config('freedom.retry.initial_sleep_seconds', 5),
                    multiplier: (float)config('freedom.retry.multiplier', 2.0),
                );

            return new FreedomManager($app->make(FreedomClientFactory::class), $logger, $retry);
        });

        $this->app->alias('freedom', FreedomManager::class);
        $this->app->bind(FreedomManagerInterface::class, FreedomManager::class);

    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/freedom.php' => config_path('freedom.php'),
        ], 'freedom-config');
    }
}
