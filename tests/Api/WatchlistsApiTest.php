<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\WatchlistsApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddWatchlistTickerRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeleteWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeleteWatchlistTickerRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\SelectWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\UpdateWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\WatchlistsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class WatchlistsApiTest extends TestCase
{
    public function test_it_sends_get_user_stock_lists_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"userStockLists":[]}')]);
        (new WatchlistsApi($this->client($mock)))->lists(new WatchlistsRequest());

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getUserStockLists', $form['cmd']);
        $this->assertArrayNotHasKey('params', $form);
    }

    public function test_it_sends_add_stock_list_command(): void
    {
        $form = $this->sentForm(fn (WatchlistsApi $api) => $api->add(
            new AddWatchlistRequest('etf', ':)', ['AAAU.US']),
        ));

        $this->assertSame('addStockList', $form['cmd']);
        $this->assertSame('etf', $form['params']['name']);
        $this->assertSame(':)', $form['params']['picture']);
        $this->assertSame(['AAAU.US'], $form['params']['tickers']);
    }

    public function test_it_sends_update_stock_list_command(): void
    {
        $form = $this->sentForm(fn (WatchlistsApi $api) => $api->update(
            new UpdateWatchlistRequest(2, 'etf2', '', 0),
        ));

        $this->assertSame('updateStockList', $form['cmd']);
        $this->assertSame('2', $form['params']['id']);
        $this->assertSame('etf2', $form['params']['name']);
        $this->assertSame('', $form['params']['picture']);
        $this->assertSame('0', $form['params']['index']);
    }

    public function test_it_sends_delete_stock_list_command(): void
    {
        $form = $this->sentForm(fn (WatchlistsApi $api) => $api->delete(new DeleteWatchlistRequest(3)));

        $this->assertSame('deleteStockList', $form['cmd']);
        $this->assertSame('3', $form['params']['id']);
    }

    public function test_it_sends_add_stock_list_ticker_command(): void
    {
        $form = $this->sentForm(fn (WatchlistsApi $api) => $api->addTicker(
            new AddWatchlistTickerRequest(2, 'AAPL.US', 2),
        ));

        $this->assertSame('addStockListTicker', $form['cmd']);
        $this->assertSame('2', $form['params']['id']);
        $this->assertSame('AAPL.US', $form['params']['ticker']);
        $this->assertSame('2', $form['params']['index']);
    }

    public function test_it_sends_delete_stock_list_ticker_command(): void
    {
        $form = $this->sentForm(fn (WatchlistsApi $api) => $api->deleteTicker(
            new DeleteWatchlistTickerRequest(2, 'AAPL.US'),
        ));

        $this->assertSame('deleteStockListTicker', $form['cmd']);
        $this->assertSame('2', $form['params']['id']);
        $this->assertSame('AAPL.US', $form['params']['ticker']);
    }

    public function test_it_sends_make_stock_list_selected_command(): void
    {
        $form = $this->sentForm(fn (WatchlistsApi $api) => $api->select(new SelectWatchlistRequest(2)));

        $this->assertSame('makeStockListSelected', $form['cmd']);
        $this->assertSame('2', $form['params']['id']);
    }

    /**
     * @param callable(WatchlistsApi): mixed $callback
     * @return array<string, mixed>
     */
    private function sentForm(callable $callback): array
    {
        $mock = new MockHandler([new Response(200, [], '{"userStockLists":[]}')]);
        $callback(new WatchlistsApi($this->client($mock)));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        return $form;
    }

    private function client(MockHandler $mock): FreedomClient
    {
        return new FreedomClient(
            new FreedomTransport(
                new Client(['handler' => HandlerStack::create($mock)]),
                new FreedomHttpConfig(retries: 0, retryDelay: 0),
            ),
            new V2Signer('private-key'),
            'public-key',
            FreedomHttpClient::V2,
            'https://api.test',
        );
    }
}
