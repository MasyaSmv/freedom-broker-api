<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\ClientInstructionHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CpsFilesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class InstructionsApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function clientHistory(ClientInstructionHistoryRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function getFiles(CpsFilesRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
