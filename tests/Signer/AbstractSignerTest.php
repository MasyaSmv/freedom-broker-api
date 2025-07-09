<?php
// tests/Signer/AbstractSignerTest.php
namespace MasyaSmv\FreedomBrokerApi\Tests\Signer;

use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\AbstractSigner;
use PHPUnit\Framework\TestCase;

final class AbstractSignerTest extends TestCase
{
    /** Тестируем рекурсивную сортировку и join без публичного метода */
    public function test_pre_sign_recursively_sorts_and_concatenates(): void
    {
        $signer = new class extends AbstractSigner {
            public function sign(array $payload): string { return ''; }
            public function expose(array $data): string   { return $this->preSign($data); }
        };

        $input    = ['b' => ['z' => 2, 'a' => 1], 'a' => 'foo'];
        $expected = 'a=foo&b=a=1&z=2';

        $this->assertSame($expected, $signer->expose($input));
    }

    public function test_pre_sign_handles_nested_and_scalar(): void
    {
        $anon = new class extends AbstractSigner {
            public function sign(array $payload): string { return ''; }
            public function expose(array $data): string  { return $this->preSign($data); }
        };

        // вложенный массив
        $nested = ['b' => ['x' => 1], 'a' => 2];
        $this->assertSame('a=2&b=x=1', $anon->expose($nested));

        // только скаляры → else-ветка
        $simple = ['b' => 2, 'a' => 1];
        $this->assertSame('a=1&b=2', $anon->expose($simple));
    }
}
