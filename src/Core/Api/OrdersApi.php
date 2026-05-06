<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\CurrentOrdersRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DelTradeOrderRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\OrderHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PutStopLossRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PutTradeOrderRequest;
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

    public function currentOrders(CurrentOrdersRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function place(PutTradeOrderRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function cancel(DelTradeOrderRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function stopLoss(PutStopLossRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
