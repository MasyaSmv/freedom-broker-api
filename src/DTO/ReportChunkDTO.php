<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use DateTimeImmutable;
use Illuminate\Support\Collection;

/**
 * Один чанк отчёта брокера для streamBrokerReport() — payload идентичен
 * результату loadBrokerReport(), плюс мета-поля sequence/coversFrom/coversTo
 * нужные потребителю для checkpoint'ов и реконсиляции.
 *
 * coversFrom/coversTo — фактические границы окна, которые были переданы в
 * loadBrokerReport(): по ним потребитель продвигает sync_checkpoint_at, чтобы
 * resume после краша/таймаута не дублировал данные.
 */
final class ReportChunkDTO
{
    public function __construct(
        public int $sequence,
        public DateTimeImmutable $coversFrom,
        public DateTimeImmutable $coversTo,
        public AccountPlainDTO $plain,
        /** @var Collection<int, OperationDTO> */
        public Collection $operations,
        /** @var Collection<int, TransferDTO> */
        public Collection $transfers,
        /** @var Collection<int, CommissionDTO> */
        public Collection $commissions,
        /** @var Collection<int, PaymentDTO> */
        public Collection $payments,
        /** @var Collection<int, PositionDTO> */
        public Collection $positions,
        /** @var Collection<int, BalanceDTO> */
        public Collection $balances,
        public ReportSummaryDTO $summary,
        public ReportPeriodDTO $period,
    ) {
    }
}
