<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\AlertMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

final class AlertMapperTest extends TestCase
{
    public function test_it_maps_alerts_to_dtos(): void
    {
        $alerts = (new AlertMapper())->mapResponse($this->response([
            'alerts' => [
                [
                    'id'                => 76,
                    'auth_login'        => 'virtual@virtual.com',
                    'ticker'            => 'FCX.US',
                    'init_price'        => 9.8,
                    'trigger_price'     => '{"price":"105"}',
                    'quote_type'        => 'ltp',
                    'notification_type' => 'email',
                    'trigger_type'      => 'crossing',
                    'periodic'          => 0,
                    'expire'            => 0,
                    'triggered'         => '',
                    'deleted'           => '0',
                ],
                [
                    'id'                => 77,
                    'auth_login'        => 'virtual@virtual.com',
                    'ticker'            => 'AAPL.US',
                    'init_price'        => 150.0,
                    'trigger_price'     => '{"price":"200"}',
                    'quote_type'        => 'ltp',
                    'notification_type' => 'push',
                    'trigger_type'      => 'greater_then',
                    'periodic'          => 60,
                    'expire'            => 'end_of_day',
                    'triggered'         => '1',
                    'deleted'           => '0',
                ],
            ],
        ]));

        $this->assertCount(2, $alerts);

        $first = $alerts[0];
        $this->assertSame(76, $first->id);
        $this->assertSame('FCX.US', $first->ticker);
        $this->assertSame(9.8, $first->initPrice);
        $this->assertSame('{"price":"105"}', $first->triggerPrice);
        $this->assertSame('ltp', $first->quoteType);
        $this->assertSame('crossing', $first->triggerType);
        $this->assertFalse($first->triggered);
        $this->assertFalse($first->deleted);
        $this->assertSame(0, $first->expire);

        $second = $alerts[1];
        $this->assertSame(77, $second->id);
        $this->assertSame('end_of_day', $second->expire);
        $this->assertTrue($second->triggered);
        $this->assertSame(60, $second->periodic);
    }

    public function test_it_skips_error_rows(): void
    {
        $alerts = (new AlertMapper())->mapPayload([
            'alerts' => [
                ['error' => 'Instrument not found'],
            ],
        ]);

        $this->assertCount(0, $alerts);
    }

    public function test_it_returns_empty_collection_for_missing_key(): void
    {
        $this->assertCount(0, (new AlertMapper())->mapPayload([]));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function response(array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('getAlertsList', 'https://api.test/v2/cmd/getAlertsList', 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
