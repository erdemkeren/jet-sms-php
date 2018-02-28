<?php

namespace Erdemkeren\JetSms\Test;

use Mockery as M;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Erdemkeren\JetSms\ShortMessage;
use Erdemkeren\JetSms\JetSmsService;
use Erdemkeren\JetSms\ShortMessageFactory;
use Erdemkeren\JetSms\ShortMessageCollection;
use Erdemkeren\JetSms\ShortMessageCollectionFactory;
use Erdemkeren\JetSms\Http\Clients\JetSmsClientInterface;
use Erdemkeren\JetSms\Http\Responses\JetSmsResponseInterface;

class JetSmsServiceTest extends TestCase
{
    public static $functions;

    /**
     * @var JetSmsService
     */
    private $service;

    /**
     * @var JetSmsResponseInterface|MockInterface
     */
    private $response;

    /**
     * @var ShortMessage|MockInterface
     */
    private $shortMessage;

    /**
     * @var ShortMessage|MockInterface
     */
    private $shortMessage2;

    /**
     * @var JetSmsClientInterface|MockInterface
     */
    private $jetSmsClient;

    /**
     * @var ShortMessageCollection
     */
    private $shortMessageCollection;

    /**
     * @var ShortMessageFactory|MockInterface
     */
    private $shortMessageFactory;

    /**
     * @var ShortMessageCollectionFactory|MockInterface
     */
    private $shortMessageCollectionFactory;

    public function setUp()
    {
        parent::setUp();

        self::$functions = M::mock();
        $this->shortMessage = M::mock(ShortMessage::class);
        $this->shortMessage2 = M::mock(ShortMessage::class);
        $this->response = M::mock(JetSmsResponseInterface::class);
        $this->jetSmsClient = M::mock(JetSmsClientInterface::class);
        $this->shortMessageFactory = M::mock(ShortMessageFactory::class);
        $this->shortMessageCollection = M::mock(ShortMessageCollection::class);
        $this->shortMessageCollectionFactory = M::mock(ShortMessageCollectionFactory::class);

        $this->service = new JetSmsService(
            $this->jetSmsClient,
            $this->shortMessageFactory,
            $this->shortMessageCollectionFactory
        );
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    public function test_it_sends_one_short_message_to_one_recipient()
    {
        $this->shortMessageFactory->shouldReceive('create')
            ->once()
            ->with('recipient', 'message')
            ->andReturn($this->shortMessage);

        $this->jetSmsClient->shouldReceive('sendShortMessage')
            ->once()
            ->with($this->shortMessage)
            ->andReturn($this->response);

        $response = $this->service->sendShortMessage('recipient', 'message');

        $this->assertInstanceOf(JetSmsResponseInterface::class, $response);
    }

    public function test_it_sends_one_short_message_to_multiple_recipients()
    {
        $this->shortMessageFactory->shouldReceive('create')
            ->once()
            ->with(['recipient1', 'recipient2'], 'message')
            ->andReturn($this->shortMessage);

        $this->jetSmsClient->shouldReceive('sendShortMessage')
            ->once()
            ->with($this->shortMessage)
            ->andReturn($this->response);

        $response = $this->service->sendShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertInstanceOf(JetSmsResponseInterface::class, $response);
    }

    public function test_it_sends_multiple_short_messages_to_multiple_recipients()
    {
        $this->shortMessageFactory->shouldReceive('create')->twice()->andReturn(
            $this->shortMessage,
            $this->shortMessage2
        );

        $this->shortMessageCollectionFactory->shouldReceive('create')->once()->andReturn($this->shortMessageCollection);

        $this->shortMessageCollection->shouldReceive('push')->once()->with($this->shortMessage);
        $this->shortMessageCollection->shouldReceive('push')->once()->with($this->shortMessage2);

        $this->jetSmsClient->shouldReceive('sendShortMessages')
            ->once()
            ->with($this->shortMessageCollection)
            ->andReturn($this->response);

        $response = $this->service->sendShortMessages([
            [
                'recipient'     => 'recipient1',
                'message'       => 'message1',
            ],
            [
                'recipient'     => 'recipient2',
                'message'       => 'message2',
            ],
        ]);

        $this->assertInstanceOf(JetSmsResponseInterface::class, $response);
    }

    public function test_it_calls_the_before_and_after_callbacks()
    {
        $beforeSingleCallback = function ($message) {
            JetSmsServiceTest::$functions->beforeSingle($message);
        };

        $afterSingleCallback = function ($response, $message) {
            JetSmsServiceTest::$functions->afterSingle($response, $message);
        };

        $beforeMultipleCallback = function ($collection) {
            JetSmsServiceTest::$functions->beforeMultiple($collection);
        };

        $afterMultipleCallback = function ($response, $collection) {
            JetSmsServiceTest::$functions->afterMultiple($response, $collection);
        };

        $service = new JetSmsService(
            $this->jetSmsClient,
            $this->shortMessageFactory,
            $this->shortMessageCollectionFactory,
            $beforeSingleCallback,
            $afterSingleCallback,
            $beforeMultipleCallback,
            $afterMultipleCallback
        );
        $this->shortMessageFactory->shouldReceive('create')
            ->once()
            ->with('recipient', 'message')
            ->andReturn($this->shortMessage);

        $this->jetSmsClient->shouldReceive('sendShortMessage')
            ->once()
            ->with($this->shortMessage)
            ->andReturn($this->response);

        JetSmsServiceTest::$functions->shouldReceive('beforeSingle')->with($this->shortMessage)->once();
        JetSmsServiceTest::$functions->shouldReceive('afterSingle')->with($this->response, $this->shortMessage)->once();

        $service->sendShortMessage('recipient', 'message');

        JetSmsServiceTest::$functions->shouldReceive('beforeMultiple')->with($this->shortMessageCollection)->once();
        JetSmsServiceTest::$functions->shouldReceive('afterMultiple')->with($this->response, $this->shortMessageCollection)->once();
        $this->shortMessageFactory->shouldReceive('create')->twice()->andReturn(
            $this->shortMessage,
            $this->shortMessage2
        );

        $this->shortMessageCollectionFactory->shouldReceive('create')->once()->andReturn($this->shortMessageCollection);

        $this->shortMessageCollection->shouldReceive('push')->once()->with($this->shortMessage);
        $this->shortMessageCollection->shouldReceive('push')->once()->with($this->shortMessage2);

        $this->jetSmsClient->shouldReceive('sendShortMessages')
            ->once()
            ->with($this->shortMessageCollection)
            ->andReturn($this->response);

        $service->sendShortMessages([
            [
                'recipient'     => 'recipient1',
                'message'       => 'message1',
            ],
            [
                'recipient'     => 'recipient2',
                'message'       => 'message2',
            ],
        ]);
    }
}
