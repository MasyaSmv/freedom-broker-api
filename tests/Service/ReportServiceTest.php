<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Service;

use GuzzleHttp\Exception\GuzzleException;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ReportServiceTest extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function test_load_fetches_and_parses_report(): void
    {
        $raw = ['report' => ['plainAccountInfoData' => []]];
        /** @var FreedomHttpClient&MockObject $http */
        $http = $this->createMock(FreedomHttpClient::class);
        $http->expects($this->once())
            ->method('request')
            ->with('getBrokerReport', [
                'date_start' => '2024-01-01',
                'date_end' => '2024-02-01',
                'time_period' => '23:59:59',
                'format' => 'json',
            ], true)
            ->willReturn($raw);

        $service = new ReportService($http, new ReportParser());
        $result = $service->load('2024-01-01', '2024-02-01');

        $this->assertArrayHasKey('operations', $result);
        $this->assertArrayHasKey('positions', $result);
    }
}
