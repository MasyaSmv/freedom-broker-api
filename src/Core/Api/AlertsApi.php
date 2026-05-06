<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddPriceAlertRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeletePriceAlertRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PriceAlertsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class AlertsApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function list(PriceAlertsRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function add(AddPriceAlertRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function delete(DeletePriceAlertRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
