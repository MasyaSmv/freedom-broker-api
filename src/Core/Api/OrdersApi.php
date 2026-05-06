<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\OrderHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class OrdersApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function history(OrderHistoryRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
