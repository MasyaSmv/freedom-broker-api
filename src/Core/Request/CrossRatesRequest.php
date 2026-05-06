<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class CrossRatesRequest implements CommandRequestInterface
{
    /**
     * @param string[] $currencies
     */
    public function __construct(
        private string $baseCurrency,
        private array $currencies,
        private ?string $date = null,
    ) {
    }

    public function command(): string
    {
        return 'getCrossRatesForDate';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = [
            'base_currency' => $this->baseCurrency,
            'currencies'    => $this->currencies,
        ];

        if ($this->date !== null) {
            $params['date'] = $this->date;
        }

        return $params;
    }
}
