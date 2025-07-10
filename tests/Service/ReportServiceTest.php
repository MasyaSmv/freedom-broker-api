<?php

// tests/Service/ReportServiceTest.php

namespace MasyaSmv\FreedomBrokerApi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use PHPUnit\Framework\TestCase;

final class ReportServiceTest extends TestCase
{
    public function test_broker_report_returns_collections(): void
    {
        // фиктивный ответ = объединяем файлы
        $body = json_encode([
            'accounts'   => json_decode(file_get_contents(__DIR__.'/../../accounts.json'), true),
            'operations' => json_decode(file_get_contents(__DIR__.'/../../operations.json'), true),
            'portfolio'  => json_decode(file_get_contents(__DIR__.'/../../portfolios.json'), true),
        ]);

        $mock  = new MockHandler([new Response(200, [], $body)]);
        $http  = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('x'),
            apiKey:'',
            version:1,
            apiUrl:'https://api'
        );

        $service = new ReportService($http, new ReportParser());

        $report  = $service->brokerReport('2025-01-01', '2025-01-31');

        $this->assertArrayHasKey('accounts', $report);
        $this->assertArrayHasKey('operations', $report);
        $this->assertArrayHasKey('positions', $report);
        $this->assertGreaterThan(0, $report['accounts']->count());
    }
}
