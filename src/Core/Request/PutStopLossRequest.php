<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\CommandRequestInterface;

final class PutStopLossRequest implements CommandRequestInterface
{
    public function __construct(
        private string $instrName,
        private ?float $takeProfit = null,
        private ?float $stopLoss = null,
        private ?float $stopLossPercent = null,
        private ?float $stoplossTailingPercent = null,
    ) {
    }

    public function command(): string
    {
        return 'putStopLoss';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = ['instr_name' => $this->instrName];

        if ($this->takeProfit !== null) {
            $params['take_profit'] = $this->takeProfit;
        }

        if ($this->stopLoss !== null) {
            $params['stop_loss'] = $this->stopLoss;
        }

        if ($this->stopLossPercent !== null) {
            $params['stop_loss_percent'] = $this->stopLossPercent;
        }

        if ($this->stoplossTailingPercent !== null) {
            $params['stoploss_trailing_percent'] = $this->stoplossTailingPercent;
        }

        return $params;
    }
}
