<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Service;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use MasyaSmv\FreedomBrokerApi\Core\Exception\InvalidResponseException;
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

    public function test_load_retries_transport_failures_up_to_three_attempts(): void
    {
        $raw = ['report' => ['plainAccountInfoData' => []]];
        $attempts = 0;
        $request = new Request('POST', 'https://api.test');
        /** @var FreedomHttpClient&MockObject $http */
        $http = $this->createMock(FreedomHttpClient::class);
        $http->expects($this->exactly(3))
            ->method('request')
            ->willReturnCallback(function () use (&$attempts, $request, $raw) {
                if (++$attempts < 3) {
                    throw new ConnectException("Timeout $attempts", $request);
                }

                return $raw;
            });

        $result = (new ReportService($http))->load('2024-01-01', '2024-02-01');

        $this->assertArrayHasKey('operations', $result);
    }

    public function test_load_throws_after_three_transport_failures(): void
    {
        $attempts = 0;
        $request = new Request('POST', 'https://api.test');
        /** @var FreedomHttpClient&MockObject $http */
        $http = $this->createMock(FreedomHttpClient::class);
        $http->expects($this->exactly(3))
            ->method('request')
            ->willReturnCallback(function () use (&$attempts, $request) {
                throw new ConnectException('Timeout ' . ++$attempts, $request);
            });

        $this->expectException(ConnectException::class);
        $this->expectExceptionMessage('Timeout 3');

        (new ReportService($http))->load('2024-01-01', '2024-02-01');
    }

    public function test_load_does_not_retry_invalid_api_responses(): void
    {
        /** @var FreedomHttpClient&MockObject $http */
        $http = $this->createMock(FreedomHttpClient::class);
        $http->expects($this->once())
            ->method('request')
            ->willThrowException(new InvalidResponseException('Invalid response'));

        $this->expectException(InvalidResponseException::class);

        (new ReportService($http))->load('2024-01-01', '2024-02-01');
    }
}
