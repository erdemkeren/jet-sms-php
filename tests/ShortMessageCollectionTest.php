<?php

namespace Erdemkeren\JetSms\Test;

use Mockery as M;
use PHPUnit_Framework_TestCase;
use Erdemkeren\JetSms\ShortMessage;
use Erdemkeren\JetSms\ShortMessageCollection;

class ShortMessageCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ShortMessage|M\MockInterface
     */
    private $shortMessage;

    /**
     * @var ShortMessage|M\MockInterface
     */
    private $shortMessage2;

    public function setUp()
    {
        parent::setUp();

        $this->shortMessage = M::mock(ShortMessage::class);
        $this->shortMessage2 = M::mock(ShortMessage::class);
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    public function test_it_only_accepts_short_messages_with_single_recipients_to_be_pushed()
    {
        $collection = new ShortMessageCollection();

        $this->shortMessage->shouldReceive('hasManyReceivers')->once()->andReturn(false);

        $collection->push($this->shortMessage);

        $this->shortMessage->shouldReceive('hasManyReceivers')->once()->andReturn(true);

        $e = null;

        try {
            $collection->push($this->shortMessage);
        } catch (\Exception $e) {

        }

        $this->assertInstanceOf(\LogicException::class, $e);
    }

    public function test_it_can_be_casted_to_xml()
    {
        $collection = new ShortMessageCollection();

        $this->shortMessage->shouldReceive('hasManyReceivers')->once()->andReturn(false);
        $this->shortMessage2->shouldReceive('hasManyReceivers')->once()->andReturn(false);
        $this->shortMessage->shouldReceive('toMultipleMessagesXml')->once()->andReturn('xml1');
        $this->shortMessage2->shouldReceive('toMultipleMessagesXml')->once()->andReturn('xml2');

        $collection->push($this->shortMessage);
        $collection->push($this->shortMessage2);

        $this->assertEquals('xml1xml2', $collection->toXml());
    }

    public function test_it_can_be_casted_to_array()
    {
        $collection = new ShortMessageCollection();

        $this->shortMessage->shouldReceive('hasManyReceivers')->once()->andReturn(false);
        $this->shortMessage2->shouldReceive('hasManyReceivers')->once()->andReturn(false);
        $this->shortMessage->shouldReceive('body')->once()->andReturn('message1');
        $this->shortMessage2->shouldReceive('body')->once()->andReturn('message2');
        $this->shortMessage->shouldReceive('receiversString')->once()->andReturn('receiver1');
        $this->shortMessage2->shouldReceive('receiversString')->once()->andReturn('receiver2');

        $collection->push($this->shortMessage);
        $collection->push($this->shortMessage2);

        $this->assertEquals([
            'Msisdns' => 'receiver1|receiver2',
            'Messages' => 'message1|message2',
        ], $collection->toArray());
    }
}
