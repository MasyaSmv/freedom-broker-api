<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritiesListRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class SecuritiesApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function list(SecuritiesListRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
