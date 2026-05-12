<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\PortfolioAccountDTO;
use MasyaSmv\FreedomBrokerApi\DTO\PortfolioPositionDTO;

final class PortfolioMapper
{
    /**
     * @return array{positions: Collection<int, PortfolioPositionDTO>, accounts: Collection<int, PortfolioAccountDTO>}
     */
    public function mapResponse(ApiResponse $response): array
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{positions: Collection<int, PortfolioPositionDTO>, accounts: Collection<int, PortfolioAccountDTO>}
     */
    public function mapPayload(array $payload): array
    {
        // Реальный tradernet API возвращает `{result: {ps: {acc:[…], pos:[…]}}}`.
        // Для обратной совместимости с тестовыми фикстурами также поддерживаем
        // старый формат с `acc`/`pos` на верхнем уровне.
        $source = (is_array($payload['result']['ps'] ?? null) ? $payload['result']['ps'] : null)
            ?? (is_array($payload['ps'] ?? null) ? $payload['ps'] : null)
            ?? $payload;

        $positions = collect();
        $accounts = collect();

        $posRows = $source['pos'] ?? [];
        if (is_array($posRows)) {
            foreach ($posRows as $row) {
                if (is_array($row)) {
                    $positions->push($this->mapPosition($row));
                }
            }
        }

        $accRows = $source['acc'] ?? [];
        if (is_array($accRows)) {
            foreach ($accRows as $row) {
                if (is_array($row)) {
                    $accounts->push($this->mapAccount($row));
                }
            }
        }

        return ['positions' => $positions, 'accounts' => $accounts];
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapPosition(array $row): PortfolioPositionDTO
    {
        return new PortfolioPositionDTO(
            ticker: (string)($row['i'] ?? ''),
            quantity: (float)($row['q'] ?? 0),
            currency: (string)($row['curr'] ?? ''),
            currencyRate: (float)($row['currval'] ?? 1),
            name: (string)($row['name'] ?? ''),
            name2: (string)($row['name2'] ?? ''),
            marketPrice: (float)($row['mkt_price'] ?? 0),
            marketValue: (float)($row['market_value'] ?? 0),
            openBalance: (float)($row['open_bal'] ?? 0),
            profitClose: (float)($row['profit_close'] ?? 0),
            profitPrice: (float)($row['profit_price'] ?? 0),
            closePrice: (float)($row['close_price'] ?? 0),
            raw: $row,
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapAccount(array $row): PortfolioAccountDTO
    {
        return new PortfolioAccountDTO(
            currency: (string)($row['curr'] ?? ''),
            currencyRate: (float)($row['currval'] ?? 1),
            freeFunds: (float)($row['s'] ?? 0),
            forecastIn: (float)($row['forecast_in'] ?? 0),
            forecastOut: (float)($row['forecast_out'] ?? 0),
            raw: $row,
        );
    }
}
