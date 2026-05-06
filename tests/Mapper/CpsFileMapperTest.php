<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\CpsFileMapper;
use PHPUnit\Framework\TestCase;

final class CpsFileMapperTest extends TestCase
{
    private CpsFileMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new CpsFileMapper();
    }

    public function test_it_maps_files(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/cps_files_fixture.json'),
            true,
        );

        $files = $this->mapper->mapPayload($fixture);

        $this->assertCount(1, $files);

        $file = $files->first();
        $this->assertSame('base64=JVBERi0xLjUK', $file->file);
        $this->assertSame('application/pdf', $file->mime);
        $this->assertSame('att_file.pdf', $file->fileName);
        $this->assertSame('pdf', $file->extension);
        $this->assertSame('base64', $file->encodingType);
    }

    public function test_it_handles_empty_files(): void
    {
        $files = $this->mapper->mapPayload(['files' => []]);

        $this->assertCount(0, $files);
    }
}
