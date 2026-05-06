<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\CashFlowsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CpsFilesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CrossRatesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CurrentOrdersRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DepositaryReportRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\GetPortfolioRequest;
use PHPUnit\Framework\TestCase;

final class P1RequestTest extends TestCase
{
    public function test_get_portfolio_request(): void
    {
        $req = new GetPortfolioRequest();
        $this->assertSame('getPositionJson', $req->command());
        $this->assertSame([], $req->toPayload());
    }

    public function test_cross_rates_request_with_date(): void
    {
        $req = new CrossRatesRequest('USD', ['EUR', 'HKD'], '2024-05-01');
        $this->assertSame('getCrossRatesForDate', $req->command());
        $payload = $req->toPayload();
        $this->assertSame('USD', $payload['base_currency']);
        $this->assertSame(['EUR', 'HKD'], $payload['currencies']);
        $this->assertSame('2024-05-01', $payload['date']);
    }

    public function test_cross_rates_request_without_date(): void
    {
        $req = new CrossRatesRequest('RUB', ['USD']);
        $payload = $req->toPayload();
        $this->assertArrayNotHasKey('date', $payload);
    }

    public function test_current_orders_request_with_active_only(): void
    {
        $req = new CurrentOrdersRequest(1);
        $this->assertSame('getNotifyOrderJson', $req->command());
        $this->assertSame(['active_only' => 1], $req->toPayload());
    }

    public function test_current_orders_request_empty(): void
    {
        $req = new CurrentOrdersRequest();
        $this->assertSame([], $req->toPayload());
    }

    public function test_cps_files_request(): void
    {
        $req = new CpsFilesRequest(id: 12345);
        $this->assertSame('getCpsFiles', $req->command());
        $this->assertSame(['id' => 12345], $req->toPayload());
    }

    public function test_cps_files_request_with_internal_id(): void
    {
        $req = new CpsFilesRequest(internalId: 99);
        $payload = $req->toPayload();
        $this->assertArrayNotHasKey('id', $payload);
        $this->assertSame(99, $payload['internal_id']);
    }

    public function test_depositary_report_request(): void
    {
        $req = new DepositaryReportRequest('2020-06-04', '2020-06-14', '23:59:59', 'json');
        $this->assertSame('getDepositaryReport', $req->command());
        $payload = $req->toPayload();
        $this->assertSame('2020-06-04', $payload['date_start']);
        $this->assertSame('2020-06-14', $payload['date_end']);
        $this->assertSame('23:59:59', $payload['time_period']);
        $this->assertSame('json', $payload['format']);
    }

    public function test_depositary_report_request_recent(): void
    {
        $req = new DepositaryReportRequest(recent: 1);
        $payload = $req->toPayload();
        $this->assertSame(1, $payload['recent']);
        $this->assertArrayNotHasKey('date_start', $payload);
    }

    public function test_cash_flows_request(): void
    {
        $req = new CashFlowsRequest(take: 10, skip: 5, groupByType: 1);
        $this->assertSame('getUserCashFlows', $req->command());
        $payload = $req->toPayload();
        $this->assertSame(10, $payload['take']);
        $this->assertSame(5, $payload['skip']);
        $this->assertSame(1, $payload['groupByType']);
    }

    public function test_cash_flows_request_empty(): void
    {
        $req = new CashFlowsRequest();
        $this->assertSame([], $req->toPayload());
    }
}
