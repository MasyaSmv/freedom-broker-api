<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use DateTimeImmutable;
use Exception;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\MarketStatusDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockHistoryDTO;

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
     * @param array<string, mixed> $payload
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
}
