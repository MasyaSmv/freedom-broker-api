<?php
// src/Core/Service/StockHistoryService.php
namespace MasyaSmv\FreedomBrokerApi\Core\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\DTO\StockHistoryDTO;

final class StockHistoryService
{
    public function __construct(
        private FreedomHttpClient $client,
        private int $rateLimit = 20
    ) {
    }

    /** @return Collection<StockHistoryDTO>
     * @throws Exception
     * @throws GuzzleException
     */
    public function history(string $ticker, DateTimeInterface $from, DateTimeInterface $to): Collection
    {
        static $count = 0;
        if (++$count > $this->rateLimit) {
            sleep(60);
            $count = 1;
        }

        $resp = $this->client->request('quotes.getHloc', [
            'id' => $ticker,
            'timeframe' => 1440,
            'intervalMode' => 'ClosedRay',
            'count' => -1,
            'date_from' => $from->format('Y-m-d'),
            'date_to' => $to->format('Y-m-d'),
        ], asArray: true);

        $out = collect();
        if (!isset($resp['hloc'][$ticker], $resp['xSeries'][$ticker])) {
            return $out;
        }

        foreach ($resp['hloc'][$ticker] as $i => $row) {
            $out->push(
                new StockHistoryDTO(
                    ticker: $ticker,
                    date: new DateTimeImmutable($resp['xSeries'][$ticker][$i]),
                    open: $row[2],
                    high: $row[0],
                    low: $row[1],
                    close: $row[3],
                ),
            );
        }

        return $out;
    }
}
