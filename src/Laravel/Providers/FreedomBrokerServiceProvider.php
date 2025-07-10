<?php

namespace MasyaSmv\FreedomBrokerApi\Laravel\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Service\OrderService;
use MasyaSmv\FreedomBrokerApi\Core\Service\QuoteInfoService;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use MasyaSmv\FreedomBrokerApi\Core\Service\StockHistoryService;
use MasyaSmv\FreedomBrokerApi\Core\Service\StockService;
use MasyaSmv\FreedomBrokerApi\Laravel\FreedomManager;

class FreedomBrokerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FreedomHttpClient::class, function () {
            return new FreedomHttpClient(
                http: Http::timeout(15)->buildClient(),
                signer: new V1Signer(config('freedom.private_key')),
                apiKey: config('freedom.public_key'),
                version: FreedomHttpClient::V1,
            );
        });

        $this->app->bind(ReportService::class, function ($app) {
            return new ReportService(
                $app->make(FreedomHttpClient::class),
                new ReportParser(),
            );
        });

        $this->app->bind(StockService::class);
        $this->app->bind(QuoteInfoService::class);
        $this->app->bind(StockHistoryService::class);

        $this->app->bind(OrderService::class, fn ($app) => new OrderService($app->make(FreedomHttpClient::class)));

        $this->app->singleton(ReportService::class, function ($app) {
            return new FreedomManager();
        });
    }

    public function boot(): void
    {
        // публикуем ТОЛЬКО конфиг
        $this->publishes([
            __DIR__ . '/../../../config/freedom.php' => config_path('freedom.php'),
        ], 'freedom-config');
    }
}
