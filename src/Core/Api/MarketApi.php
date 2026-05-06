<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\MarketStatusRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class MarketApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function quoteInfo(QuoteInfoRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function quoteHistory(QuoteHistoryRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function marketStatus(MarketStatusRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
