<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use DateTimeImmutable;
use Exception;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\AllowedTickerDTO;
use MasyaSmv\FreedomBrokerApi\DTO\MarketStatusDTO;
use MasyaSmv\FreedomBrokerApi\DTO\OptionContractDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockHistoryDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockQuoteDTO;
use MasyaSmv\FreedomBrokerApi\DTO\TickerSearchResultDTO;
use MasyaSmv\FreedomBrokerApi\DTO\TradeHistoryDTO;
use MasyaSmv\FreedomBrokerApi\DTO\TradesDTO;

final class MarketMapper
{
    /**
     * @throws Exception
     */
    public function mapQuoteInfoResponse(ApiResponse $response): ?StockDTO
    {
        return $this->mapQuoteInfoPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<int|string, mixed> $payload
     * @throws Exception
     */
    public function mapQuoteInfoPayload(array $payload): ?StockDTO
    {
        if (!isset($payload['c'])) {
            return null;
        }

        return new StockDTO(
            ticker: (string)$payload['c'],
            freedomId: (int)($payload['n'] ?? 0),
            name: (string)($payload['short_name'] ?? $payload['name'] ?? ''),
            instrType: (string)($payload['instr_type_c'] ?? ''),
            currency: $payload['curr'] ?? null,
            lastPrice: isset($payload['l']) ? (float)$payload['l'] : null,
            lastTradeAt: isset($payload['ltt']) ? new DateTimeImmutable((string)$payload['ltt']) : null,
            raw: $payload,
        );
    }

    /**
     * @return Collection<int, StockHistoryDTO>
     * @throws Exception
     */
    public function mapQuoteHistoryResponse(string $ticker, ApiResponse $response): Collection
    {
        return $this->mapQuoteHistoryPayload($ticker, $response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, StockHistoryDTO>
     * @throws Exception
     */
    public function mapQuoteHistoryPayload(string $ticker, array $payload): Collection
    {
        $out = collect();

        if (!isset($payload['hloc'][$ticker], $payload['xSeries'][$ticker])) {
            return $out;
        }

        $hloc = $payload['hloc'][$ticker];
        $xSeries = $payload['xSeries'][$ticker];

        if (!is_array($hloc) || !is_array($xSeries)) {
            return $out;
        }

        foreach ($hloc as $index => $row) {
            if (!is_array($row) || !isset($xSeries[$index], $row[0], $row[1], $row[2], $row[3])) {
                continue;
            }

            $out->push(new StockHistoryDTO(
                ticker: $ticker,
                date: new DateTimeImmutable((string)$xSeries[$index]),
                open: (float)$row[2],
                high: (float)$row[0],
                low: (float)$row[1],
                close: (float)$row[3],
            ));
        }

        return $out;
    }

    /**
     * @return Collection<int, MarketStatusDTO>
     */
    public function mapMarketStatusResponse(ApiResponse $response): Collection
    {
        return $this->mapMarketStatusPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, MarketStatusDTO>
     */
    public function mapMarketStatusPayload(array $payload): Collection
    {
        $out = collect();
        $markets = $payload['result']['markets'] ?? [];

        if (!is_array($markets)) {
            return $out;
        }

        $requestedAt = isset($markets['t']) ? (string)$markets['t'] : null;
        $rows = $markets['m'] ?? [];

        if (!is_array($rows)) {
            return $out;
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $out->push($this->mapMarketStatusRow($row, $requestedAt));
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapMarketStatusRow(array $row, ?string $requestedAt = null): MarketStatusDTO
    {
        return new MarketStatusDTO(
            market: (string)($row['n2'] ?? ''),
            name: (string)($row['n'] ?? ''),
            status: (string)($row['s'] ?? ''),
            opensAt: (string)($row['o'] ?? ''),
            closesAt: isset($row['c']) ? (string)$row['c'] : null,
            timeOffsetMinutes: isset($row['dt']) ? (int)$row['dt'] : null,
            requestedAt: $requestedAt,
            raw: $row,
        );
    }

    /**
     * @return Collection<int, StockQuoteDTO>
     */
    public function mapStockQuotesResponse(ApiResponse $response): Collection
    {
        return $this->mapStockQuotesPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, StockQuoteDTO>
     */
    public function mapStockQuotesPayload(array $payload): Collection
    {
        $out = collect();
        $rows = $payload['result']['q'] ?? [];

        if (!is_array($rows)) {
            return $out;
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $out->push($this->mapStockQuoteRow($row));
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapStockQuoteRow(array $row): StockQuoteDTO
    {
        return new StockQuoteDTO(
            ticker: (string)($row['c'] ?? ''),
            name: (string)($row['name'] ?? ''),
            name2: (string)($row['name2'] ?? ''),
            lastPrice: $this->toFloat($row['ltp'] ?? $row['l'] ?? null),
            previousClose: $this->toFloat($row['pp'] ?? null),
            change: $this->toFloat($row['chg'] ?? null),
            percentChange: $this->toFloat($row['pcp'] ?? null),
            bestBid: $this->toFloat($row['bbp'] ?? null),
            bestAsk: $this->toFloat($row['bap'] ?? null),
            volume: $this->toInt($row['vol'] ?? null),
            lastTradeAt: isset($row['ltt']) ? (string)$row['ltt'] : null,
            raw: $row,
        );
    }

    /**
     * @return Collection<int, TickerSearchResultDTO>
     */
    public function mapTickerFinderResponse(ApiResponse $response): Collection
    {
        return $this->mapTickerFinderPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, TickerSearchResultDTO>
     */
    public function mapTickerFinderPayload(array $payload): Collection
    {
        $out = collect();
        $rows = $payload['found'] ?? $payload['result']['found'] ?? [];

        if (!is_array($rows)) {
            return $out;
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $out->push($this->mapTickerFinderRow($row));
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapTickerFinderRow(array $row): TickerSearchResultDTO
    {
        return new TickerSearchResultDTO(
            instrumentId: (int)($row['instr_id'] ?? 0),
            ticker: (string)($row['t'] ?? ''),
            name: (string)($row['n'] ?? ''),
            longName: (string)($row['nm'] ?? ''),
            englishName: (string)($row['ln'] ?? ''),
            isin: (string)($row['isin'] ?? ''),
            type: (int)($row['type'] ?? 0),
            kind: (int)($row['kind'] ?? 0),
            market: (string)($row['mkt'] ?? ''),
            marketId: (int)($row['mkt_id'] ?? 0),
            exchangeCode: (string)($row['code_nm'] ?? ''),
            raw: $row,
        );
    }

    private function toFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float)$value : null;
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (int)$value : null;
    }

    /**
     * @return Collection<int, TradesDTO>
     */
    public function mapTradesResponse(ApiResponse $response): Collection
    {
        return $this->mapTradesPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, TradesDTO>
     */
    public function mapTradesPayload(array $payload): Collection
    {
        $out = collect();

        foreach ($payload as $ticker => $block) {
            if ($ticker === 'took' || !is_array($block)) {
                continue;
            }

            $series = $block['series'] ?? [];
            $info = $block['info'] ?? [];

            $out->push(new TradesDTO(
                ticker: (string)$ticker,
                series: is_array($series) ? array_values($series) : [],
                info: is_array($info) ? $info : [],
                raw: $block,
            ));
        }

        return $out;
    }

    /**
     * @return array{items: Collection<int, TradeHistoryDTO>, maxTradeId: ?string}
     */
    public function mapTradesHistoryResponse(ApiResponse $response): array
    {
        return $this->mapTradesHistoryPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{items: Collection<int, TradeHistoryDTO>, maxTradeId: ?string}
     */
    public function mapTradesHistoryPayload(array $payload): array
    {
        $trades = $payload['trades'] ?? [];
        $items = collect();

        if (!is_array($trades)) {
            return ['items' => $items, 'maxTradeId' => null];
        }

        $maxTradeRows = $trades['max_trade_id'] ?? [];
        $maxTradeId = is_array($maxTradeRows) && isset($maxTradeRows[0]['@text'])
            ? (string)$maxTradeRows[0]['@text']
            : null;

        $rows = $trades['trade'] ?? [];
        if (!is_array($rows)) {
            return ['items' => $items, 'maxTradeId' => $maxTradeId];
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $items->push($this->mapTradeHistoryRow($row));
            }
        }

        return ['items' => $items, 'maxTradeId' => $maxTradeId];
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapTradeHistoryRow(array $row): TradeHistoryDTO
    {
        return new TradeHistoryDTO(
            id: (string)($row['id'] ?? ''),
            orderId: (string)($row['order_id'] ?? ''),
            ticker: (string)($row['instr_nm'] ?? ''),
            price: (float)($row['p'] ?? 0),
            quantity: (float)($row['q'] ?? 0),
            value: (float)($row['v'] ?? $row['summ'] ?? 0),
            date: (string)($row['date'] ?? ''),
            currency: (string)($row['curr_c'] ?? ''),
            type: (int)($row['type'] ?? 0),
            raw: $row,
        );
    }

    /**
     * @return Collection<int, string>
     */
    public function mapTopSecuritiesResponse(ApiResponse $response): Collection
    {
        $tickers = $response->payloadOrFail()->get('tickers', []);

        if (!is_array($tickers)) {
            return collect();
        }

        return collect(array_values(array_map('strval', $tickers)));
    }

    public function mapAllowedTickerResponse(ApiResponse $response): AllowedTickerDTO
    {
        $payload = $response->payloadOrFail()->data();
        $allowedExpires = $payload['allowedExpires'] ?? [];

        return new AllowedTickerDTO(
            allowed: (bool)($payload['allowed'] ?? false),
            allowedExpires: is_array($allowedExpires) ? array_map('intval', $allowedExpires) : [],
            restriction: isset($payload['restriction']) ? (string)$payload['restriction'] : null,
            operation: isset($payload['operation']) ? (string)$payload['operation'] : null,
            recommendations: is_array($payload['recommendations'] ?? null) ? $payload['recommendations'] : [],
            raw: $payload,
        );
    }

    /**
     * @return Collection<int, OptionContractDTO>
     */
    public function mapOptionsByMarketResponse(ApiResponse $response): Collection
    {
        return $this->mapOptionsByMarketPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, OptionContractDTO>
     */
    public function mapOptionsByMarketPayload(array $payload): Collection
    {
        $out = collect();

        foreach ($payload as $row) {
            if (is_array($row)) {
                $out->push(new OptionContractDTO(
                    ticker: (string)($row['ticker'] ?? ''),
                    baseContractCode: (string)($row['base_contract_code'] ?? ''),
                    lastTradeDate: (string)($row['last_trade_date'] ?? ''),
                    expireDate: (string)($row['expire_date'] ?? ''),
                    strikePrice: (float)($row['strike_price'] ?? 0),
                    optionType: (string)($row['option_type'] ?? ''),
                    raw: $row,
                ));
            }
        }

        return $out;
    }
}
