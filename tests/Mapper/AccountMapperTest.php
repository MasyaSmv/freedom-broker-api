<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\AccountMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

final class AccountMapperTest extends TestCase
{
    public function test_it_maps_plain_account_payload_to_profile_dto(): void
    {
        $profile = (new AccountMapper())->mapResponse($this->response([
            'plainAccountInfoData' => [
                'client_code' => 'TEST1234',
                'client_name' => 'John Doe',
                'account_type' => ' Личный счет физического лица ',
                'base_currency' => 'USD',
                'tariff_name' => 'Advisory',
            ],
        ]));

        $this->assertSame('TEST1234', $profile->clientCode);
        $this->assertSame('John Doe', $profile->clientName);
        $this->assertSame('Личный счет физического лица', $profile->accountType);
        $this->assertSame('USD', $profile->baseCurrency);
        $this->assertSame('Advisory', $profile->tariff);
        $this->assertArrayHasKey('plainAccountInfoData', $profile->raw);
    }

    public function test_it_keeps_raw_payload_when_profile_shape_is_sparse(): void
    {
        $profile = (new AccountMapper())->mapResponse($this->response([
            'ok' => true,
            'account' => 'A1',
        ]));

        $this->assertSame('A1', $profile->clientCode);
        $this->assertNull($profile->clientName);
        $this->assertSame(['ok' => true, 'account' => 'A1'], $profile->raw);
    }

    private function response(array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('getOPQ', 'https://api.test/v2/cmd/getOPQ', 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
