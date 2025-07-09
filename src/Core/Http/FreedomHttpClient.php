<?php
// src/Core/Http/FreedomHttpClient.php

namespace MasyaSmv\FreedomBrokerApi\Core\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\SignerInterface;
use RuntimeException;

/**
 * Полноценный клиент Freedom Broker API
 *  App\Helpers\BrokerParsers\Freedom\FreedomApiService.
 *
 * ─ Поддерживает V1 и V2 протоколы подписи;
 * ─ Умеет добавлять SID;
 * ─ Формирует nonce в микросекундах × 10 000;
 * ─ Возвращает decoded-JSON (array|stdClass).
 *
 * Требует PHP 8.0 и Guzzle 7.
 */
class FreedomHttpClient
{
    public const V1 = 1;
    public const V2 = 2;

    private string $apiUrl;
    private int $version;
    private string $apiKey;
    private SignerInterface $signer;
    private ClientInterface $http;
    private ?string $sid = null;

    public function __construct(
        ClientInterface $http,
        SignerInterface $signer,
        string $apiKey = '',
        int $version = self::V1,
        ?string $apiUrl = null
    ) {
        $this->http = $http;
        $this->signer = $signer;
        $this->apiKey = $apiKey;
        $this->version = $version;
        $this->apiUrl = $apiUrl ?: 'https://tradernet.ru/api';
    }

    /** Устанавливает SID (метод chain-style). */
    public function withSid(string $sid): self
    {
        $clone = clone $this;
        $clone->sid = $sid;

        return $clone;
    }

    /**
     * @param string $method cmd-метод Freedom API
     * @param array|null $params params
     * @param bool $asArray true → array, false → stdClass
     *
     * @throws GuzzleException
     */
    public function request(string $method, ?array $params = null, bool $asArray = false): mixed
    {
        // 1. Собираем payload
        $payload = [
            'cmd' => $method,
            'nonce' => (int)(microtime(true) * 10000),
        ];

        if ($params) {
            $payload['params'] = $params;
        }
        if ($this->sid) {
            $payload['SID'] = $this->sid;
        }
        if ($this->version !== self::V1 && $this->apiKey) {
            $payload['apiKey'] = $this->apiKey;
        }

        // 2. Подпись
        $headers = [];
        if ($this->version === self::V1) {
            $payload['sig'] = $this->signer->sign($payload);
        } else {
            $headers['X-NtApi-Sig'] = $this->signer->sign($payload);
        }

        // 3. URL и тело
        $url = $this->version === self::V1
            ? $this->apiUrl
            : "$this->apiUrl/v2/cmd/$method";

        $body = $this->version === self::V1
            ? ['q' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]
            : $payload;

        // 4. Запрос
        $response = $this->http->request('POST', $url, [
            RequestOptions::FORM_PARAMS => $body,
            RequestOptions::HEADERS => $headers,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::VERIFY => false,
        ]);

        $json = (string)$response->getBody();
        if ($json === '') {
            throw new RuntimeException('Freedom API: empty response');
        }

        return json_decode($json, $asArray);
    }

    /**
     * Позволяет изменить базовый API-URL
     *
     * @param string $apiUrl
     *
     * @return void
     */
    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }
}
