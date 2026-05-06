<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\GetPortfolioRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class PortfolioApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function info(): ApiResponse
    {
        $request = new GetPortfolioRequest();
        return $this->client->request($request->command(), $request->toPayload());
    }
}
