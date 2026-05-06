<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\DelTradeOrderRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PutStopLossRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\PutTradeOrderRequest;
use PHPUnit\Framework\TestCase;

final class OrderWriteRequestTest extends TestCase
{
    public function test_put_trade_order_command(): void
    {
        $req = new PutTradeOrderRequest('AAPL.US', 1, 2, 100, 1);
        $this->assertSame('putTradeOrder', $req->command());
    }

    public function test_put_trade_order_required_params(): void
    {
        $req = new PutTradeOrderRequest('AAPL.US', 1, 2, 100, 3);
        $payload = $req->toPayload();

        $this->assertSame('AAPL.US', $payload['instr_name']);
        $this->assertSame(1, $payload['action_id']);
        $this->assertSame(2, $payload['order_type_id']);
        $this->assertSame(100, $payload['qty']);
        $this->assertSame(3, $payload['expiration_id']);
        $this->assertArrayNotHasKey('limit_price', $payload);
        $this->assertArrayNotHasKey('stop_price', $payload);
        $this->assertArrayNotHasKey('user_order_id', $payload);
    }

    public function test_put_trade_order_optional_params(): void
    {
        $req = new PutTradeOrderRequest('AAPL.US', 1, 2, 100, 1, 150.5, 140.0, 99999);
        $payload = $req->toPayload();

        $this->assertSame(150.5, $payload['limit_price']);
        $this->assertSame(140.0, $payload['stop_price']);
        $this->assertSame(99999, $payload['user_order_id']);
    }

    public function test_del_trade_order_command_and_payload(): void
    {
        $req = new DelTradeOrderRequest(2929292929);
        $this->assertSame('delTradeOrder', $req->command());
        $this->assertSame(['order_id' => 2929292929], $req->toPayload());
    }

    public function test_put_stop_loss_command(): void
    {
        $req = new PutStopLossRequest('SIE.EU');
        $this->assertSame('putStopLoss', $req->command());
    }

    public function test_put_stop_loss_required_only(): void
    {
        $req = new PutStopLossRequest('SIE.EU');
        $payload = $req->toPayload();

        $this->assertSame('SIE.EU', $payload['instr_name']);
        $this->assertArrayNotHasKey('take_profit', $payload);
        $this->assertArrayNotHasKey('stop_loss', $payload);
        $this->assertArrayNotHasKey('stop_loss_percent', $payload);
        $this->assertArrayNotHasKey('stoploss_trailing_percent', $payload);
    }

    public function test_put_stop_loss_with_all_params(): void
    {
        $req = new PutStopLossRequest('SIE.EU', 1.5, 0.9, 5.0, 3.0);
        $payload = $req->toPayload();

        $this->assertSame(1.5, $payload['take_profit']);
        $this->assertSame(0.9, $payload['stop_loss']);
        $this->assertSame(5.0, $payload['stop_loss_percent']);
        $this->assertSame(3.0, $payload['stoploss_trailing_percent']);
    }
}
