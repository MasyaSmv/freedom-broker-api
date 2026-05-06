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
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\Laravel\FreedomManager;

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
            return new FreedomManager($app->make(FreedomClientFactory::class));
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
