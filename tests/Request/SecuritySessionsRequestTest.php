<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritySessionsRequest;
use PHPUnit\Framework\TestCase;

final class SecuritySessionsRequestTest extends TestCase
{
    public function test_command_and_empty_payload(): void
    {
        $req = new SecuritySessionsRequest();
        $this->assertSame('getSecuritySessions', $req->command());
        $this->assertSame([], $req->toPayload());
    }
}
