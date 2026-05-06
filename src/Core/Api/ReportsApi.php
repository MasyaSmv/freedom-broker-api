<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\BrokerReportRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class ReportsApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function brokerReport(BrokerReportRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
