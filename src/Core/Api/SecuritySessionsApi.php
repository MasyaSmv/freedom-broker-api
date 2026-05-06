<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritySessionsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class SecuritySessionsApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function list(SecuritySessionsRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
