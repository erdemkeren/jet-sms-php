<?php

namespace Erdemkeren\JetSms\Test;

use Mockery as M;
use PHPUnit\Framework\TestCase;
use Erdemkeren\JetSms\ShortMessage;

class ShortMessageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    public function test_it_constructs_with_single_recipient()
    {
        $shortMessage = new ShortMessage('recipient', 'message');

        $this->assertEquals(['recipient'], $shortMessage->receivers());
        $this->assertEquals('recipient', $shortMessage->receiversString('|'));
        $this->assertEquals('message', $shortMessage->body());
    }

    public function test_it_constructs_with_multiple_recipients()
    {
        $shortMessage = new ShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertTrue($shortMessage->hasManyReceivers());
        $this->assertEquals(['recipient1', 'recipient2'], $shortMessage->receivers());
        $this->assertEquals('recipient1|recipient2', $shortMessage->receiversString('|'));
        $this->assertEquals('message', $shortMessage->body());
    }

    public function test_it_can_be_casted_to_array()
    {
        $shortMessage = new ShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertEquals([
            'Msisdns' => 'recipient1|recipient2',
            'Messages' => 'message',
        ], $shortMessage->toArray());
    }

    public function test_it_can_be_casted_to_single_message_xml()
    {
        $shortMessage = new ShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertEquals(
            '<text>message</text><message><gsmnos>recipient1,recipient2</gsmnos></message>',
            $shortMessage->toSingleMessageXml()
        );
    }

    public function test_it_can_be_casted_to_multiple_message_xml()
    {
        $shortMessage = new ShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertEquals(
            '<message><gsmno>recipient1,recipient2</gsmno><text>message</text></message>',
            $shortMessage->toMultipleMessagesXml()
        );
    }
}
