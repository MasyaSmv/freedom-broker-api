<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\CrossRatesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class CurrencyApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function crossRates(CrossRatesRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
