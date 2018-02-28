<?php

namespace Erdemkeren\JetSms\Test;

use Mockery as M;
use PHPUnit\Framework\TestCase;
use Erdemkeren\JetSms\ShortMessage;
use Erdemkeren\JetSms\ShortMessageFactory;

class ShortMessageFactoryTest extends TestCase
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

    public function test_it_creates_new_short_messages()
    {
        $shortMessageFactory = new ShortMessageFactory();

        $shortMessage = $shortMessageFactory->create('receiver', 'message');

        $this->assertInstanceOf(ShortMessage::class, $shortMessage);
        $this->assertEquals('message', $shortMessage->body());
        $this->assertEquals('receiver', $shortMessage->receiversString());
    }
}
