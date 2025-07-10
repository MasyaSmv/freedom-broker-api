<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

/**
 * ⚠️  Реальный HTTP-запрос к «https://tradernet.ru/api».
 * Запускается только если выставлена переменная окружения FREEDOM_LIVE_TEST=1
 */
final class FreedomHttpClientTest extends TestCase
{
    private const PUBLIC_KEY  = '3771cc1f44163f5399127f563b8342ce';
    private const PRIVATE_KEY = '';   // пустой

    /**
     * @throws GuzzleException
     */
    public function test_it_gets_non_empty_response(): void
    {
        if (!\getenv('FREEDOM_LIVE_TEST')) {
            $this->markTestSkipped('Live test disabled – set FREEDOM_LIVE_TEST=1');
        }

        $client = new FreedomHttpClient(
            http    : new Client(['timeout' => 10]),
            signer  : new V1Signer(self::PRIVATE_KEY),
            apiKey  : self::PUBLIC_KEY,
            version : FreedomHttpClient::V1,
        );

        $resp = $client->request('ping', null, true); // array

        // Достаточно убедиться, что пришёл непустой массив
        $this->assertIsArray($resp);
        $this->assertNotEmpty($resp);
    }

    /** проверяем setApiUrl() и withSid() одновременно
     *
     * @throws GuzzleException
     */
    public function test_with_sid_and_custom_url(): void
    {
        $mock   = new MockHandler([new Response(200, [], '{}')]);
        $client = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('s'),
            apiKey  : '',
            version : 1,
            apiUrl  : 'https://foo/api'
        );

        $client->setApiUrl('https://bar/api');
        $client = $client->withSid('SID123');
        $client->request('ping');

        $req = $mock->getLastRequest();
        $this->assertSame('https://bar/api', (string) $req->getUri());
        $body = (string) $req->getBody();
        $this->assertStringContainsString('"SID":"SID123"', urldecode($body));
    }

    public function test_empty_response_throws_runtime_exception(): void
    {
        $mock = new MockHandler([new Response(200, [], '')]); // пустое тело
        $cli  = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('s'),
            apiKey:'',
            version:1,
            apiUrl:'https://x/api'
        );

        $this->expectException(RuntimeException::class);
        $cli->request('ping');
    }

    /**
     * @throws GuzzleException
     */
    public function test_with_sid_and_set_api_url_are_respected(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $cli  = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('k'),
            apiKey:'',
            version:1,
            apiUrl:'https://foo/api'
        );

        $cli->setApiUrl('https://bar/api');
        $cli = $cli->withSid('SID42');
        $cli->request('ping');

        $req = $mock->getLastRequest();
        $this->assertSame('https://bar/api', (string)$req->getUri());

        parse_str($req->getBody()->getContents(), $form);
        $json = json_decode($form['q'], true);
        $this->assertSame('SID42', $json['SID']);
    }

    /**
     * @throws GuzzleException
     */
    public function test_empty_response_throws_runtime(): void
    {
        $mock = new MockHandler([new Response(200, [], '')]);
        $cli  = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('x'),
            apiKey:'',
            version:1,
            apiUrl:'https://z/api'
        );

        $this->expectException(RuntimeException::class);
        $cli->request('ping');
    }

    /**
     * @throws GuzzleException
     */
    public function test_request_in_version2_path_covers_all_branches(): void
    {
        // Freedom API отвечает чем-угодно – нам важен сам факт non-empty ответа
        $mock   = new MockHandler([new Response(200, [], '{"stub":42}')]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $api    = new FreedomHttpClient(
            http    : $client,
            signer  : new V2Signer('priv'),
            apiKey  : 'pub',
            version : FreedomHttpClient::V2,          // ← ключевой момент
            apiUrl  : 'https://api.test'
        );

        $params = ['foo' => 'bar'];
        $api->request('auth-login', $params, asArray: true);   // выполняем

        /** @var RequestInterface $req */
        $req   = $mock->getLastRequest();
        $body  = $req->getBody()->getContents();   // form-url-encoded строка
        parse_str($body, $form);

        // --- проверки на каждую «красную» ветку ---
        $this->assertSame('auth-login', $form['cmd']);           // базовый cmd
        $this->assertSame($params, $form['params']);
        $this->assertSame('pub', $form['apiKey']);         // строка 80
        $this->assertArrayHasKey('nonce', $form);

        // заголовок подписи (строка 88)
        $this->assertTrue($req->hasHeader('X-NtApi-Sig'));

        // URL V2 (строка 94)
        $this->assertSame('https://api.test/v2/cmd/auth-login', (string)$req->getUri());
    }
}
