<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Response;

use MasyaSmv\FreedomBrokerApi\Core\Exception\ApiRequestFailedException;

final class ApiResponse
{
    public function __construct(
        private ApiMeta $meta,
        private ApiRequestContext $context,
        private ?ApiPayload $payload,
        private ?ApiError $error,
        private string $rawBody
    ) {
    }

    public function successful(): bool
    {
        return $this->error === null && $this->meta->statusCode() < 400;
    }

    public function payload(): ?ApiPayload
    {
        return $this->payload;
    }

    public function payloadOrFail(): ApiPayload
    {
        if ($this->payload === null || !$this->successful()) {
            throw new ApiRequestFailedException($this);
        }

        return $this->payload;
    }

    public function error(): ?ApiError
    {
        return $this->error;
    }

    public function meta(): ApiMeta
    {
        return $this->meta;
    }

    public function context(): ApiRequestContext
    {
        return $this->context;
    }

    public function rawBody(): string
    {
        return $this->rawBody;
    }
}
