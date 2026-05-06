<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class SecuritiesListRequest implements CommandRequestInterface
{
    /**
     * @param array<int, array<string, mixed>> $filter
     */
    public function __construct(
        private int $take = 50,
        private int $skip = 0,
        private array $filter = [
            ['field' => 'instr_type_c', 'operator' => 'neq', 'value' => 4],
        ]
    ) {
        if ($this->take < 1) {
            throw new InvalidArgumentException('Securities list request take must be greater than zero.');
        }

        if ($this->skip < 0) {
            throw new InvalidArgumentException('Securities list request skip must not be negative.');
        }
    }

    public function command(): string
    {
        return 'securities.getList';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'take' => $this->take,
            'skip' => $this->skip,
            'filter' => $this->filter,
        ];
    }
}
