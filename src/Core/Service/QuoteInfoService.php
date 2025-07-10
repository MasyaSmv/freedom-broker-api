<?php

// src/Core/Service/QuoteInfoService.php

namespace MasyaSmv\FreedomBrokerApi\Core\Service;

use DateTimeImmutable;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;

final class QuoteInfoService
{
    public function __construct(
        private FreedomHttpClient $client,
        private int $rateLimit = 30          // вызовов в минуту
    ) {
    }

    /**
     * @param string $ticker
     *
     * @return StockDTO|null
     * @throws GuzzleException
     * @throws Exception
     */
    public function get(string $ticker): ?StockDTO
    {
        static $cnt = 0;
        if (++$cnt > $this->rateLimit) {
            sleep(60);
            $cnt = 1;
        }

        $data = $this->client->request('quotes.getInfo', ['ticker' => $ticker], asArray: true);
        return isset($data['c']) ? $this->map($data) : null;
    }

    /**
     * @param array $d
     *
     * @return StockDTO
     * @throws Exception
     */
    private function map(array $d): StockDTO
    {
        return new StockDTO(
            ticker: $d['c'],
            freedomId: (int)($d['n'] ?? 0),
            name: $d['short_name'] ?? $d['name'] ?? '',
            instrType: (string)($d['instr_type_c'] ?? ''),
            currency: $d['curr'] ?? null,
            lastPrice: $d['l'] ?? null,
            lastTradeAt: isset($d['ltt']) ? new DateTimeImmutable($d['ltt']) : null,
            raw: $d,
        );
    }
}
