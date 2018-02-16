<?php

namespace Erdemkeren\JetSms\Test\Clients;

use Mockery as M;
use GuzzleHttp\Client;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use Erdemkeren\JetSms\ShortMessage;
use Psr\Http\Message\ResponseInterface;
use Erdemkeren\JetSms\ShortMessageCollection;
use Erdemkeren\JetSms\Http\Clients\JetSmsHttpClient;
use Erdemkeren\JetSms\Http\Responses\JetSmsResponseInterface;

class JetSmsHttpClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client|MockInterface
     */
    private $httpClient;

    /**
     * @var ShortMessage|MockInterface
     */
    private $shortMessage;

    /**
     * @var ShortMessageCollection|MockInterface
     */
    private $shortMessageCollection;

    /**
     * @var Client|ResponseInterface
     */
    private $httpResponse;

    public function setUp()
    {
        parent::setUp();

        $this->httpClient = M::mock(Client::class);
        $this->shortMessage = M::mock(ShortMessage::class);
        $this->shortMessageCollection = M::mock(ShortMessageCollection::class);
        $this->httpResponse = M::mock(ResponseInterface::class);
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    public function test_it_sends_single_short_message()
    {
        $client = new JetSmsHttpClient(
            $this->httpClient,
            'foo',
            'bar',
            'baz',
            'qux'
        );

        $this->shortMessage->shouldReceive('toArray')->once()->andReturn([
            'foo' => 'bar',
        ]);

        $this->httpResponse->shouldReceive('getBody')->once()->andReturn("Status=0\r\nMessageIDs=151103141334228\r\nType=Fake\r\n");

        $this->httpClient->shouldReceive('request')->with('POST', 'foo', [
            'form_params' => [
                'foo'            => 'bar',
                'SendDate'       => null,
                'Username'       => 'bar',
                'Password'       => 'baz',
                'TransmissionID' => 'qux',
            ],
        ])->andReturn($this->httpResponse);

        $response = $client->sendShortMessage($this->shortMessage);

        $this->assertInstanceOf(JetSmsResponseInterface::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_it_sends_multiple_short_messages()
    {
        $client = new JetSmsHttpClient(
            $this->httpClient,
            'foo',
            'bar',
            'baz',
            'qux'
        );

        $this->shortMessageCollection->shouldReceive('toArray')->once()->andReturn([
            'foo' => 'bar',
            'baz' => 'qux',
        ]);

        $this->httpResponse->shouldReceive('getBody')->once()->andReturn("Status=0\r\nMessageIDs=151103141334228|151103141334229\r\nType=Fake\r\n");

        $this->httpClient->shouldReceive('request')->with('POST', 'foo', [
            'form_params' => [
                'foo'            => 'bar',
                'baz'            => 'qux',
                'SendDate'       => null,
                'Username'       => 'bar',
                'Password'       => 'baz',
                'TransmissionID' => 'qux',
            ],
        ])->andReturn($this->httpResponse);

        $response = $client->sendShortMessages($this->shortMessageCollection);

        $this->assertInstanceOf(JetSmsResponseInterface::class, $response);
        $this->assertTrue($response->isSuccessful());
    }
}
