<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\CommandRequestInterface;

final class PutTradeOrderRequest implements CommandRequestInterface
{
    public function __construct(
        private string $instrName,
        private int $actionId,
        private int $orderTypeId,
        private int $qty,
        private int $expirationId,
        private ?float $limitPrice = null,
        private ?float $stopPrice = null,
        private ?int $userOrderId = null,
    ) {
    }

    public function command(): string
    {
        return 'putTradeOrder';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = [
            'instr_name'    => $this->instrName,
            'action_id'     => $this->actionId,
            'order_type_id' => $this->orderTypeId,
            'qty'           => $this->qty,
            'expiration_id' => $this->expirationId,
        ];

        if ($this->limitPrice !== null) {
            $params['limit_price'] = $this->limitPrice;
        }

        if ($this->stopPrice !== null) {
            $params['stop_price'] = $this->stopPrice;
        }

        if ($this->userOrderId !== null) {
            $params['user_order_id'] = $this->userOrderId;
        }

        return $params;
    }
}
