<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\ClientInstructionMapper;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\ClientInstructionDTO;
use PHPUnit\Framework\TestCase;

final class ClientInstructionMapperTest extends TestCase
{
    public function test_it_maps_fixture_response(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/client_instruction_history_fixture.json'),
            true,
        );

        $result = (new ClientInstructionMapper())->mapResponse($this->response($fixture));

        $this->assertSame(1, $result['total']);
        $this->assertCount(1, $result['items']);

        $dto = $result['items'][0];
        $this->assertInstanceOf(ClientInstructionDTO::class, $dto);
        $this->assertSame(13123125345345, $dto->id);
        $this->assertSame(10160, $dto->typeDocId);
        $this->assertSame(3, $dto->statusCode);
        $this->assertSame('Копию SMS в Telegram', $dto->name);
        $this->assertSame('2019-11-06 15:31:55', $dto->dateCreated);
        $this->assertSame('johndow@example.com', $dto->ownerLogin);
        $this->assertFalse($dto->isBlocked);
        $this->assertFalse($dto->availableForCancel);
    }

    public function test_it_returns_empty_for_bad_shape(): void
    {
        $result = (new ClientInstructionMapper())->mapPayload(['cps' => 'bad']);

        $this->assertSame(0, $result['total']);
        $this->assertCount(0, $result['items']);
    }

    private function response(array $payload): ApiResponse
    {
        return new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('getClientCpsHistory', 'https://api.test/v2/cmd/getClientCpsHistory', 'POST', 2, []),
            new ApiPayload($payload),
            null,
            json_encode($payload) ?: '',
        );
    }
}
