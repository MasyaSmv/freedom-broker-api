<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

interface CommandRequestInterface
{
    public function command(): string;

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array;
}
