<?php

namespace MasyaSmv\FreedomBrokerApi\Laravel;

use DateTime;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportSummaryDTO;

class FreedomManager
{
    /**
     * Загружает отчёт брокера за указанный период.
     *
     * @param string|null $publicKey публичный ключ клиента
     * @param string|null $privateKey секретный ключ клиента
     * @param string|null $from начало периода в 'Y-m-d', по умолчанию 20 лет назад
     * @param string|null $to конец периода в 'Y-m-d', по умолчанию сегодня
     * @param int $version версия API (V1|V2)
     *
     * @return array{plain:AccountPlainDTO, operations:Collection, positions:Collection, balances:Collection, summary:ReportSummaryDTO}
     * @throws GuzzleException
     */
    public function load(
        ?string $publicKey = null,
        ?string $privateKey = null,
        ?string $from = null,
        ?string $to = null,
        int $version = FreedomHttpClient::V1
    ): array {
        // 1. Вычисляем даты по умолчанию
        $from = $from ?: (new DateTime('-30 years'))->format('Y-m-d');
        $to = $to ?: (new DateTime())->format('Y-m-d');

        // 2. Ключи из параметров или из конфига
        $publicKey = $publicKey ?: (string)config('freedom.public_key');
        $privateKey = $privateKey ?: (string)config('freedom.private_key');

        // 3. Инстанцируем HTTP-клиент и signer
        $http = new FreedomHttpClient(
            http: new GuzzleClient(['timeout' => 15]),
            signer: $version === FreedomHttpClient::V2
                ? new V2Signer($privateKey)
                : new V1Signer($privateKey),
            apiKey: $publicKey,
            version: $version,
            apiUrl: config('freedom.api_url', 'https://tradernet.ru/api'),
        );

        // 4. Парсер и сервис
        $parser = new ReportParser();
        // 5. Загружаем
        return (new ReportService($http, $parser))->load($from, $to);
    }
}
