<?php

namespace MasyaSmv\FreedomBrokerApi\Laravel\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use MasyaSmv\FreedomBrokerApi\Laravel\FreedomManager;

class FreedomBrokerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 1) Привязываем GuzzleHttp\ClientInterface → GuzzleHttp\Client
        $this->app->bind(ClientInterface::class, function () {
            return new Client([
                'timeout' => config('freedom.timeout', 15),
                'verify' => config('freedom.verify_ssl', false),
            ]);
        });

        // 2) Привязываем FreedomHttpClient
        $this->app->singleton(FreedomHttpClient::class, function ($app) {
            $pub = config('freedom.public_key');
            $priv = config('freedom.private_key');
            $ver = config('freedom.version', FreedomHttpClient::V2);
            $signer = $ver === FreedomHttpClient::V2
                ? new V2Signer($priv)
                : new V1Signer($priv);

            return new FreedomHttpClient(
                http: $app->make(ClientInterface::class),
                signer: $signer,
                apiKey: $pub,
                version: $ver,
                apiUrl: config('freedom.api_url', 'https://tradernet.ru/api'),
            );
        });

        // 3) Привязываем ReportService (он зависит от FreedomHttpClient и ReportParser)
        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService(
                $app->make(FreedomHttpClient::class),
                new ReportParser(),
            );
        });

        // 4) Если вы сделали FreedomManager, то биндите его под фасад:
        $this->app->singleton('freedom', function ($app) {
            return new FreedomManager();
        });

        $this->app->alias(FreedomManager::class, 'freedom');
    }

    public function boot(): void
    {
        // публикуем ТОЛЬКО конфиг
        $this->publishes([
            __DIR__ . '/../../../config/freedom.php' => config_path('freedom.php'),
        ], 'freedom-config');
    }
}
