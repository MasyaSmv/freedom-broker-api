<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Client;

use MasyaSmv\FreedomBrokerApi\Core\Exception\SigningException;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\SignerInterface;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponseDecoder;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use Throwable;

final class FreedomClient
{
    private ?string $sid = null;

    public function __construct(
        private FreedomTransport $transport,
        private SignerInterface $signer,
        private string $apiKey = '',
        private int $version = FreedomHttpClient::V2,
        private string $apiUrl = 'https://tradernet.ru/api',
        ?ApiResponseDecoder $decoder = null
    ) {
        $this->apiUrl = rtrim($this->apiUrl, '/');
        $this->decoder = $decoder ?? new ApiResponseDecoder();
    }

    private ApiResponseDecoder $decoder;

    public function withSid(string $sid): self
    {
        $clone = clone $this;
        $clone->sid = $sid;

        return $clone;
    }

    /**
     * @param array<string, mixed>|null $params
     */
    public function request(string $command, ?array $params = null): ApiResponse
    {
        $payload = [
            'cmd' => $command,
            'nonce' => (int)(microtime(true) * 10000),
        ];

        if ($params !== null && $params !== []) {
            $payload['params'] = $params;
        }

        if ($this->sid !== null) {
            $payload['SID'] = $this->sid;
        }

        if ($this->version !== FreedomHttpClient::V1 && $this->apiKey !== '') {
            $payload['apiKey'] = $this->apiKey;
        }

        $headers = [];

        try {
            if ($this->version === FreedomHttpClient::V1) {
                $payload['sig'] = $this->signer->sign($payload);
            } else {
                $headers['X-NtApi-Sig'] = $this->signer->sign($payload);
            }
        } catch (Throwable $exception) {
            throw new SigningException(
                sprintf('Freedom API signing failed for command [%s].', $command),
                $exception,
            );
        }

        $url = $this->version === FreedomHttpClient::V1
            ? $this->apiUrl
            : sprintf('%s/v2/cmd/%s', $this->apiUrl, $command);

        $formParams = $this->version === FreedomHttpClient::V1
            ? ['q' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]
            : $payload;

        $context = new ApiRequestContext($command, $url, 'POST', $this->version, $payload);
        $response = $this->transport->postForm($url, $formParams, $headers);

        return $this->decoder->decode($response, $context);
    }
}
