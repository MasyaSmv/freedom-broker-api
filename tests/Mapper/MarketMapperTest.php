<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use DateTimeInterface;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\MarketMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

final class MarketMapperTest extends TestCase
{
    public function test_it_maps_quote_info_payload_to_stock_dto(): void
    {
        $stock = (new MarketMapper())->mapQuoteInfoResponse($this->response('quotes.getInfo', [
            'c' => 'ABC.US',
            'n' => 123,
            'short_name' => 'ABC',
            'instr_type_c' => 1,
            'curr' => 'USD',
            'l' => 10.5,
            'ltt' => '2024-01-02 10:00:00',
        ]));

        $this->assertNotNull($stock);
        $this->assertSame('ABC.US', $stock->ticker);
        $this->assertSame(123, $stock->freedomId);
        $this->assertSame(10.5, $stock->lastPrice);
        $this->assertInstanceOf(DateTimeInterface::class, $stock->lastTradeAt);
    }

    public function test_it_maps_quote_history_payload_to_history_dtos(): void
    {
        $history = (new MarketMapper())->mapQuoteHistoryResponse('ABC.US', $this->response('quotes.getHloc', [
            'hloc' => [
                'ABC.US' => [
                    [12.0, 9.0, 10.0, 11.0],
                ],
            ],
            'xSeries' => [
                'ABC.US' => ['2024-01-02'],
            ],
        ]));

        $this->assertCount(1, $history);
        $this->assertSame('ABC.US', $history[0]->ticker);
        $this->assertSame(10.0, $history[0]->open);
        $this->assertSame(12.0, $history[0]->high);
        $this->assertSame(9.0, $history[0]->low);
        $this->assertSame(11.0, $history[0]->close);
    }

    private function response(string $command, array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext($command, 'https://api.test/v2/cmd/'.$command, 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
