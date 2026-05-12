<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

/**
 * Перевод бумаг (visible-summary) — секция `report.in_outs_securities.detailed`
 * в реальном tradernet broker report. Отличается от обычной OperationDTO тем,
 * что в исходных данных НЕТ trade_id/id, нет цены и не всегда есть валюта;
 * есть `account` и `type` (например `securities_in`, `securities_out`).
 *
 * Идентификатор детерминированно вычисляется из набора стабильных полей с
 * разделителем — это гарантирует идемпотентность при повторном парсинге.
 */
final class TransferDTO
{
    /**
     * @param array<string,mixed> $raw полный сырой массив из API
     */
    public function __construct(
        public string $id,
        public string $type,
        public string $dateTime,
        public string $ticker,
        public string $isin,
        public float $quantity,
        public ?string $account,
        public ?string $comment,
        public array $raw = [],
    ) {
    }
}
