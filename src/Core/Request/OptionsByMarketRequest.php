<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class OptionsByMarketRequest implements CommandRequestInterface
{
    public function __construct(
        private string $market,
        private string $baseContractCode,
    ) {
        if ($this->market === '' || $this->baseContractCode === '') {
            throw new InvalidArgumentException('Options request market and base contract code must not be empty.');
        }
    }

    public function command(): string
    {
        return 'getOptionsByMktNameAndBaseAsset';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'ltr' => $this->market,
            'base_contract_code' => $this->baseContractCode,
        ];
    }
}
