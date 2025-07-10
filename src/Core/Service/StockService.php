<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Service;

use DateTimeImmutable;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;

final class StockService
{
    public function __construct(
        private FreedomHttpClient $client,
        private int $pageSize = 50,   // сколько бумаг за один вызов
        private int $rateLimit = 6      // запросов в минуту
    ) {
    }

    /**
     * @return Collection
     * @throws GuzzleException
     * @throws Exception
     */
    public function all(): Collection
    {
        $skip = 0;
        $result = collect();
        $delay = 60 / $this->rateLimit;

        while (true) {
            $payload = [
                'take' => $this->pageSize,
                'skip' => $skip,
                'filter' => [
                    // исключаем опционы (instr_type_c = 4)
                    ['field' => 'instr_type_c', 'operator' => 'neq', 'value' => 4],
                ],
            ];

            $resp = $this->client->request('securities.getList', $payload, asArray: true);
            $rows = $resp['securities'] ?? [];

            if (!$rows) {
                break;
            }

            foreach ($rows as $row) {
                $result->push($this->mapRow($row));
            }

            $skip += $this->pageSize;
            usleep((int)($delay * 1_000_000));
        }

        return $result;
    }

    /**
     * @param array $r
     *
     * @return StockDTO
     * @throws Exception
     */
    private function mapRow(array $r): StockDTO
    {
        return new StockDTO(
            ticker: $r['ticker'],
            freedomId: (int)$r['n'],
            name: $r['short_name'] ?? $r['name'],
            instrType: (string)$r['instr_type_c'],
            currency: $r['curr'] ?? null,
            lastPrice: $r['l'] ?? null,
            lastTradeAt: isset($r['ltt']) ? new DateTimeImmutable($r['ltt']) : null,
            raw: $r,
        );
    }
}
