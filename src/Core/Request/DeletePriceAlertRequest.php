<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class DeletePriceAlertRequest implements CommandRequestInterface
{
    public function __construct(
        private int $id,
        private string $quoteType = 'ltp',
        private string $notificationType = 'email',
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
            'id'                => $this->id,
            'del'               => true,
            'quote_type'        => $this->quoteType,
            'notification_type' => $this->notificationType,
        ];
    }
}
