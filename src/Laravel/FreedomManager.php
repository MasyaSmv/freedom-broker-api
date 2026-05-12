<?php

namespace MasyaSmv\FreedomBrokerApi\Laravel;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Config\FreedomConfig;
use MasyaSmv\FreedomBrokerApi\Contracts\FreedomManagerInterface;
use MasyaSmv\FreedomBrokerApi\Core\Factory\FreedomClientFactory;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Retry\RetryPolicy;
use MasyaSmv\FreedomBrokerApi\Credentials\CredentialsProviderInterface;
use MasyaSmv\FreedomBrokerApi\Credentials\FreedomCredentials;
use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportPeriodDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportSummaryDTO;
use MasyaSmv\FreedomBrokerApi\FreedomSession;
use Psr\Log\LoggerInterface;

class FreedomManager implements FreedomManagerInterface
{
    private FreedomClientFactory $clients;
    private ?LoggerInterface $logger;
    private ?RetryPolicy $retryPolicy;

    public function __construct(
        ?FreedomClientFactory $clients = null,
        ?LoggerInterface $logger = null,
        ?RetryPolicy $retryPolicy = null
    ) {
        $this->clients = $clients ?? new FreedomClientFactory($this->configFromLaravel());
        $this->logger = $logger;
        $this->retryPolicy = $retryPolicy;
    }

    public function connect(string $publicKey, string $privateKey, ?string $sid = null, ?int $version = null): FreedomSession
    {
        return $this->withCredentials(new FreedomCredentials($publicKey, $privateKey, $sid), $version);
    }

    public function withCredentials(
        FreedomCredentials|CredentialsProviderInterface $credentials,
        ?int $version = null
    ): FreedomSession {
        return new FreedomSession(
            $this->clients->make($credentials, $version),
            apiClient: $this->clients->makeApiClient($credentials, $version),
            logger: $this->logger,
            retryPolicy: $this->retryPolicy,
        );
    }

    public function client(
        string $publicKey,
        string $privateKey,
        ?string $sid = null,
        ?int $version = null
    ): FreedomHttpClient {
        return $this->clients->make(new FreedomCredentials($publicKey, $privateKey, $sid), $version);
    }

    /**
     * Загружает отчёт брокера за указанный период.
     *
     * @param string|null $publicKey публичный ключ клиента
     * @param string|null $privateKey секретный ключ клиента
     * @param string|null $from начало периода в 'Y-m-d', по умолчанию 20 лет назад
     * @param string|null $to конец периода в 'Y-m-d', по умолчанию сегодня
     * @param int $version версия API (V1|V2)
     *
     * @return array{
     *     plain:AccountPlainDTO,
     *     operations:Collection,
     *     transfers:Collection,
     *     commissions:Collection,
     *     payments:Collection,
     *     positions:Collection,
     *     balances:Collection,
     *     summary:ReportSummaryDTO,
     *     period:ReportPeriodDTO
     * }
     * @throws GuzzleException
     */
    public function load(
        ?string $publicKey,
        ?string $privateKey,
        ?string $from = null,
        ?string $to = null,
        int $version = FreedomHttpClient::V2
    ): array {
        $from = $from ?: (new DateTime('-30 years'))->format('Y-m-d');
        $to = $to ?: (new DateTime())->format('Y-m-d');

        $publicKey = $publicKey ?: $this->deprecatedConfigValue('public_key');
        $privateKey = $privateKey ?: $this->deprecatedConfigValue('private_key');

        return $this->connect($publicKey, $privateKey, version: $version)
            ->loadBrokerReport($from, $to);
    }

    private function configFromLaravel(): FreedomConfig
    {
        if (!function_exists('config')) {
            return new FreedomConfig();
        }

        return FreedomConfig::fromArray((array)config('freedom', []));
    }

    private function deprecatedConfigValue(string $key): string
    {
        if (!function_exists('config')) {
            return '';
        }

        return (string)config("freedom.$key", '');
    }
}
