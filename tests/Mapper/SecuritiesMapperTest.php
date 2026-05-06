<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use DateTimeInterface;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\SecuritiesMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

final class SecuritiesMapperTest extends TestCase
{
    public function test_it_maps_securities_payload_to_stock_dtos(): void
    {
        $stocks = (new SecuritiesMapper())->mapResponse($this->response([
            'securities' => [
                [
                    'ticker' => 'ABC.US',
                    'n' => 123,
                    'short_name' => 'ABC',
                    'instr_type_c' => 1,
                    'curr' => 'USD',
                    'l' => 10.5,
                    'ltt' => '2024-01-02 10:00:00',
                ],
            ],
        ]));

        $this->assertCount(1, $stocks);
        $this->assertSame('ABC.US', $stocks[0]->ticker);
        $this->assertSame(123, $stocks[0]->freedomId);
        $this->assertSame('ABC', $stocks[0]->name);
        $this->assertSame('1', $stocks[0]->instrType);
        $this->assertSame('USD', $stocks[0]->currency);
        $this->assertSame(10.5, $stocks[0]->lastPrice);
        $this->assertInstanceOf(DateTimeInterface::class, $stocks[0]->lastTradeAt);
    }

    private function response(array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('securities.getList', 'https://api.test/v2/cmd/securities.getList', 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
