<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use DateTimeImmutable;
use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use PHPUnit\Framework\TestCase;

final class MarketRequestTest extends TestCase
{
    public function test_quote_info_request_requires_ticker(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new QuoteInfoRequest('');
    }

    public function test_quote_history_request_builds_payload(): void
    {
        $payload = (new QuoteHistoryRequest(
            'ABC.US',
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-01-31'),
        ))->toPayload();

        $this->assertSame('ABC.US', $payload['id']);
        $this->assertSame(1440, $payload['timeframe']);
        $this->assertSame('2024-01-01', $payload['date_from']);
        $this->assertSame('2024-01-31', $payload['date_to']);
    }
}
