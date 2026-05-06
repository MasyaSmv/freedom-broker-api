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

    public function test_it_maps_market_status_payload_to_dtos(): void
    {
        $statuses = (new MarketMapper())->mapMarketStatusResponse($this->response('getMarketStatus', [
            'result' => [
                'markets' => [
                    't' => '2020-11-18 19:29:27',
                    'm' => [
                        [
                            'n' => 'KASE',
                            'n2' => 'KASE',
                            's' => 'CLOSE',
                            'o' => '08:20:00',
                            'c' => '14:00:00',
                            'dt' => '-180',
                        ],
                    ],
                ],
            ],
        ]));

        $this->assertCount(1, $statuses);
        $this->assertSame('KASE', $statuses[0]->market);
        $this->assertSame('CLOSE', $statuses[0]->status);
        $this->assertSame('08:20:00', $statuses[0]->opensAt);
        $this->assertSame('14:00:00', $statuses[0]->closesAt);
        $this->assertSame(-180, $statuses[0]->timeOffsetMinutes);
        $this->assertSame('2020-11-18 19:29:27', $statuses[0]->requestedAt);
    }

    public function test_it_maps_stock_quotes_payload_to_dtos(): void
    {
        $quotes = (new MarketMapper())->mapStockQuotesResponse($this->response('getStockQuotesJson', [
            'result' => [
                'q' => [
                    [
                        'c' => 'AAPL.US',
                        'name' => 'Apple Inc.',
                        'name2' => 'Apple Inc.',
                        'ltp' => '147,42',
                        'pp' => '147,95',
                        'chg' => '-0,53',
                        'pcp' => '-0,36',
                        'bbp' => '147,39',
                        'bap' => '147,45',
                        'vol' => '129617290',
                        'ltt' => '2016-10-05T17:04:43',
                    ],
                ],
            ],
        ]));

        $this->assertCount(1, $quotes);
        $this->assertSame('AAPL.US', $quotes[0]->ticker);
        $this->assertSame(147.42, $quotes[0]->lastPrice);
        $this->assertSame(-0.53, $quotes[0]->change);
        $this->assertSame(129617290, $quotes[0]->volume);
    }

    public function test_it_maps_ticker_finder_payload_to_dtos(): void
    {
        $results = (new MarketMapper())->mapTickerFinderResponse($this->response('tickerFinder', [
            'found' => [
                [
                    'instr_id' => 10000007229,
                    't' => 'AAPL.US',
                    'n' => 'Apple',
                    'nm' => 'Apple Inc.',
                    'ln' => 'Apple Inc.',
                    'isin' => 'US0378331005',
                    'type' => 1,
                    'kind' => 1,
                    'mkt' => 'FIX',
                    'mkt_id' => 30000000001,
                    'code_nm' => 'AAPL',
                ],
            ],
        ]));

        $this->assertCount(1, $results);
        $this->assertSame(10000007229, $results[0]->instrumentId);
        $this->assertSame('AAPL.US', $results[0]->ticker);
        $this->assertSame('US0378331005', $results[0]->isin);
        $this->assertSame('FIX', $results[0]->market);
    }

    public function test_it_maps_trades_payload_to_dtos(): void
    {
        $trades = (new MarketMapper())->mapTradesResponse($this->response('getHloc', [
            'AAPL.US' => [
                'series' => [[1, 2, 3]],
                'info' => [
                    'id' => 'AAPL.US',
                    'currency' => 'USD',
                ],
            ],
            'took' => 2.759,
        ]));

        $this->assertCount(1, $trades);
        $this->assertSame('AAPL.US', $trades[0]->ticker);
        $this->assertSame([[1, 2, 3]], $trades[0]->series);
        $this->assertSame('USD', $trades[0]->info['currency']);
    }

    public function test_it_maps_trades_history_payload_to_dtos(): void
    {
        $result = (new MarketMapper())->mapTradesHistoryResponse($this->response('getTradesHistory', [
            'trades' => [
                'max_trade_id' => [
                    ['@text' => '40975888'],
                ],
                'trade' => [
                    [
                        'id' => 2229992229292,
                        'order_id' => 299998887727,
                        'p' => 141.4,
                        'q' => 20,
                        'v' => 2828,
                        'date' => '2019-08-15T10:10:22',
                        'instr_nm' => 'AAPL.US',
                        'curr_c' => 'USD',
                        'type' => 1,
                    ],
                ],
            ],
        ]));

        $this->assertSame('40975888', $result['maxTradeId']);
        $this->assertCount(1, $result['items']);
        $this->assertSame('2229992229292', $result['items'][0]->id);
        $this->assertSame('AAPL.US', $result['items'][0]->ticker);
        $this->assertSame(141.4, $result['items'][0]->price);
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
