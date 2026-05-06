<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\SecuritySessionMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

final class SecuritySessionMapperTest extends TestCase
{
    public function test_it_maps_sessions_array_to_dtos(): void
    {
        $sessions = (new SecuritySessionMapper())->mapResponse($this->response([
            [
                'id'              => 7165011,
                'owner_login'     => 'virtual@virtual.com',
                'user_login'      => 'virtual@virtual.com',
                'safety_type_id'  => 4,
                'key_current'     => 'Login, password',
                'start_datetime'  => '2016-06-14T13:14:13.187',
                'expire_datetime' => '2016-06-15T13:14:13.187',
                'expire'          => 85299,
                'ip'              => 'c5u7iip134fdfsxfqt4aq1osqchtg5b',
                'ip_client'       => '19.16.32.32',
            ],
            [
                'id'              => 7165012,
                'owner_login'     => 'virtual@virtual.com',
                'user_login'      => 'broker@example.com',
                'safety_type_id'  => 14,
                'key_current'     => 'SMS code',
                'start_datetime'  => '2016-06-14T14:00:00.000',
                'expire_datetime' => '2016-06-21T14:00:00.000',
                'expire'          => 604800,
                'ip'              => 'abc123',
                'ip_client'       => '10.0.0.1',
            ],
        ]));

        $this->assertCount(2, $sessions);

        $first = $sessions[0];
        $this->assertSame(7165011, $first->id);
        $this->assertSame('virtual@virtual.com', $first->ownerLogin);
        $this->assertSame(4, $first->safetyTypeId);
        $this->assertSame('Login, password', $first->keyCurrent);
        $this->assertSame(85299, $first->expire);
        $this->assertSame('19.16.32.32', $first->ipClient);

        $second = $sessions[1];
        $this->assertSame(7165012, $second->id);
        $this->assertSame('broker@example.com', $second->userLogin);
    }

    public function test_it_returns_empty_collection_for_empty_array(): void
    {
        $this->assertCount(0, (new SecuritySessionMapper())->mapList([]));
    }

    /**
     * @param array<int, array<string, mixed>> $payload
     */
    private function response(array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('getSecuritySessions', 'https://api.test/v2/cmd/getSecuritySessions', 'POST', 2, []),
            new ApiPayload($payload), // @phpstan-ignore-line
            null,
            json_encode($payload) ?: '',
        );
    }
}
