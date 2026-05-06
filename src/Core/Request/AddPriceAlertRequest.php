<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class AddPriceAlertRequest implements CommandRequestInterface
{
    public function __construct(
        private string $ticker,
        private string $price,
        private string $triggerType = 'crossing',
        private string $quoteType = 'ltp',
        private string $notificationType = 'email',
        private int|string $alertPeriod = 0,
        private int|string $expire = 0,
    ) {
    }

    public function command(): string
    {
        return 'togglePriceAlert';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'ticker'            => $this->ticker,
            'price'             => ['price' => $this->price],
            'trigger_type'      => $this->triggerType,
            'quote_type'        => $this->quoteType,
            'notification_type' => $this->notificationType,
            'alert_period'      => $this->alertPeriod,
            'expire'            => $this->expire,
        ];
    }
}
