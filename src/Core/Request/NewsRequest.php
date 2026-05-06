<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class NewsRequest implements CommandRequestInterface
{
    public function __construct(
        private ?int $limit = null,
        private ?string $searchFor = null,
        private ?string $ticker = null,
        private ?int $storyId = null,
    ) {
        if ($this->limit !== null && $this->limit < 1) {
            throw new InvalidArgumentException('News limit must be greater than zero.');
        }
    }

    public function command(): string
    {
        return 'getNews';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $payload = [];

        if ($this->limit !== null) {
            $payload['limit'] = $this->limit;
        }

        if (!$this->isBlank($this->searchFor)) {
            $payload['searchFor'] = $this->searchFor;
        }

        if (!$this->isBlank($this->ticker)) {
            $payload['ticker'] = $this->ticker;
        }

        if ($this->storyId !== null) {
            $payload['storyId'] = $this->storyId;
        }

        return $payload;
    }

    private function isBlank(?string $value): bool
    {
        return $value === null || trim($value) === '';
    }
}
