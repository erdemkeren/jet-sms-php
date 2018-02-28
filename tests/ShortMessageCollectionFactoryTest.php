<?php

namespace Erdemkeren\JetSms\Test;

use Mockery as M;
use PHPUnit\Framework\TestCase;
use Erdemkeren\JetSms\ShortMessageCollection;
use Erdemkeren\JetSms\ShortMessageCollectionFactory;

class ShortMessageCollectionFactoryTest extends TestCase
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

    public function test_it_creates_new_short_message_collections()
    {
        $shortMessageCollectionFactory = new ShortMessageCollectionFactory();

        $shortMessageCollection = $shortMessageCollectionFactory->create();

        $this->assertInstanceOf(ShortMessageCollection::class, $shortMessageCollection);
    }
}
