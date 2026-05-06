<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\AddPriceAlertRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeletePriceAlertRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PriceAlertsRequest;
use PHPUnit\Framework\TestCase;

final class PriceAlertRequestTest extends TestCase
{
    public function test_alerts_list_command_and_empty_payload(): void
    {
        $req = new PriceAlertsRequest();
        $this->assertSame('getAlertsList', $req->command());
        $this->assertSame([], $req->toPayload());
    }

    public function test_alerts_list_with_ticker(): void
    {
        $req = new PriceAlertsRequest('AAPL.US');
        $this->assertSame(['ticker' => 'AAPL.US'], $req->toPayload());
    }

    public function test_add_alert_command_and_payload(): void
    {
        $req = new AddPriceAlertRequest('FCX.US', '105', 'crossing', 'ltp', 'email', 0, 0);
        $this->assertSame('togglePriceAlert', $req->command());

        $payload = $req->toPayload();
        $this->assertSame('FCX.US', $payload['ticker']);
        $this->assertSame(['price' => '105'], $payload['price']);
        $this->assertSame('crossing', $payload['trigger_type']);
        $this->assertSame('ltp', $payload['quote_type']);
        $this->assertSame('email', $payload['notification_type']);
        $this->assertSame(0, $payload['alert_period']);
        $this->assertSame(0, $payload['expire']);
    }

    public function test_delete_alert_command_and_payload(): void
    {
        $req = new DeletePriceAlertRequest(76, 'ltp', 'email');
        $this->assertSame('togglePriceAlert', $req->command());

        $payload = $req->toPayload();
        $this->assertSame(76, $payload['id']);
        $this->assertTrue($payload['del']);
        $this->assertSame('ltp', $payload['quote_type']);
        $this->assertSame('email', $payload['notification_type']);
    }
}
