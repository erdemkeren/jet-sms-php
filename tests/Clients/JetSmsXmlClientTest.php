<?php

namespace Erdemkeren\JetSms\Http\Clients;

use Mockery as M;
use GuzzleHttp\Client;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Erdemkeren\JetSms\ShortMessage;
use Psr\Http\Message\ResponseInterface;
use Erdemkeren\JetSms\ShortMessageCollection;
use Erdemkeren\JetSms\Http\Responses\JetSmsResponseInterface;

function curl_init($url = null)
{
    return JetSmsXmlClientTest::$functions->curl_init($url);
}

function curl_setopt($ch, $option, $value)
{
    return JetSmsXmlClientTest::$functions->curl_setopt($ch, $option, $value);
}

function curl_exec($ch)
{
    return JetSmsXmlClientTest::$functions->curl_exec($ch);
}

class JetSmsXmlClientTest extends TestCase
{
    public static $functions;

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

        self::$functions = M::mock();
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
        $client = new JetSmsXmlClient(
            'foo',
            'bar',
            'baz',
            'qux'
        );

        $this->shortMessage->shouldReceive('toSingleMessageXml')->once()->andReturn('xml');

        self::$functions->shouldReceive('curl_init')->once()->andReturn('foo');
        self::$functions->shouldReceive('curl_setopt')->times(4);
        self::$functions->shouldReceive('curl_exec')->with('foo')->once()->andReturn('00 123');
        $response = $client->sendShortMessage($this->shortMessage);

        $this->assertInstanceOf(JetSmsResponseInterface::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_it_sends_multiple_short_messages()
    {
        $client = new JetSmsXmlClient(
            'foo',
            'bar',
            'baz',
            'qux'
        );

        $this->shortMessageCollection->shouldReceive('toXml')->once()->andReturn('xml');

        self::$functions->shouldReceive('curl_init')->once()->andReturn('foo');
        self::$functions->shouldReceive('curl_setopt')->times(4);
        self::$functions->shouldReceive('curl_exec')->with('foo')->once()->andReturn('00 123');
        $response = $client->sendShortMessages($this->shortMessageCollection);

        $this->assertInstanceOf(JetSmsResponseInterface::class, $response);
        $this->assertTrue($response->isSuccessful());
    }
}
