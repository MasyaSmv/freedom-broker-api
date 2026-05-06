<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\GetOpqRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class AccountApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function getOpq(?GetOpqRequest $request = null): ApiResponse
    {
        $request = $request ?? new GetOpqRequest();

        return $this->client->request($request->command(), $request->toPayload());
    }

    public function validateCredentials(): ApiResponse
    {
        return $this->getOpq();
    }
}
