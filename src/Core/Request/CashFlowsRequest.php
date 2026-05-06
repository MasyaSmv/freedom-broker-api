<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class CashFlowsRequest implements CommandRequestInterface
{
    /**
     * @param array<array{field: string, operator: string, value: mixed}>|null $filters
     * @param array<array{field: string, dir: string}>|null $sort
     */
    public function __construct(
        private ?int $userId = null,
        private ?int $groupByType = null,
        private ?int $take = null,
        private ?int $skip = null,
        private ?int $withoutRefund = null,
        private ?int $cashTotals = null,
        private ?int $hideLimits = null,
        private ?array $filters = null,
        private ?array $sort = null,
    ) {
    }

    public function command(): string
    {
        return 'getUserCashFlows';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = [];

        if ($this->userId !== null) {
            $params['user_id'] = $this->userId;
        }
        if ($this->groupByType !== null) {
            $params['groupByType'] = $this->groupByType;
        }
        if ($this->take !== null) {
            $params['take'] = $this->take;
        }
        if ($this->skip !== null) {
            $params['skip'] = $this->skip;
        }
        if ($this->withoutRefund !== null) {
            $params['without_refund'] = $this->withoutRefund;
        }
        if ($this->cashTotals !== null) {
            $params['cash_totals'] = $this->cashTotals;
        }
        if ($this->hideLimits !== null) {
            $params['hide_limits'] = $this->hideLimits;
        }
        if ($this->filters !== null) {
            $params['filters'] = $this->filters;
        }
        if ($this->sort !== null) {
            $params['sort'] = $this->sort;
        }

        return $params;
    }
}
