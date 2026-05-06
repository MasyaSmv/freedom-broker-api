<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Transport;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;

final class FreedomTransport
{
    private FreedomHttpConfig $config;

    public function __construct(ClientInterface $http, ?FreedomHttpConfig $config = null)
    {
        $this->http = $http;
        $this->config = $config ?? new FreedomHttpConfig();
    }

    private ClientInterface $http;

    /**
     * @param array<string, mixed> $formParams
     * @param array<string, string> $headers
     *
     * @throws GuzzleException
     */
    public function postForm(string $url, array $formParams, array $headers = []): TransportResponse
    {
        $maxAttempts = max(1, $this->config->retries() + 1);

        for ($attempts = 1; $attempts <= $maxAttempts; $attempts++) {
            try {
                $response = $this->http->request('POST', $url, [
                    RequestOptions::FORM_PARAMS => $formParams,
                    RequestOptions::HEADERS => $headers,
                    RequestOptions::HTTP_ERRORS => false,
                    RequestOptions::VERIFY => $this->config->verifySsl(),
                    RequestOptions::TIMEOUT => $this->config->timeout(),
                    RequestOptions::CONNECT_TIMEOUT => $this->config->connectTimeout(),
                ]);
            } catch (GuzzleException $exception) {
                if ($attempts >= $maxAttempts) {
                    throw $exception;
                }

                $this->delay();
                continue;
            }

            if ($response->getStatusCode() >= 500 && $attempts < $maxAttempts) {
                $this->delay();
                continue;
            }

            return new TransportResponse(
                $response->getStatusCode(),
                (string)$response->getBody(),
                $response->getHeaders(),
                $attempts,
            );
        }

        throw new \LogicException('Freedom transport retry loop ended without response.');
    }

    private function delay(): void
    {
        if ($this->config->retryDelay() > 0) {
            usleep($this->config->retryDelay() * 1000);
        }
    }
}
