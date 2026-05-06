<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Response;

final class ApiError
{
    /** @var array<string, mixed>|null */
    private ?array $details;

    /**
     * @param array<string, mixed>|null $details
     */
    public function __construct(
        private string $message,
        private int $statusCode,
        private ?string $type = null,
        ?array $details = null
    ) {
        $this->details = $details;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function type(): ?string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function details(): ?array
    {
        return $this->details;
    }
}
