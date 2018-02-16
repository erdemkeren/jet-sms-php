<?php

namespace Erdemkeren\JetSms\Test;

use Erdemkeren\JetSms\Http\Responses\JetSmsXmlResponse;

class JetSmsXmlResponseTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_returns_true_if_the_response_is_successful()
    {
        $xmlApiResponse = new JetSmsXmlResponse('00 123');

        $this->assertTrue($xmlApiResponse->isSuccessful());
    }

    public function test_it_returns_null_message_if_response_is_successful()
    {
        $xmlApiResponse = new JetSmsXmlResponse('00 123');

        $this->assertNull($xmlApiResponse->message());
    }

    public function test_it_returns_message_if_response_is_unsuccessful()
    {
        $xmlApiResponse = new JetSmsXmlResponse('10 Invalid Credentials');

        $this->assertEquals('Invalid Credentials', $xmlApiResponse->message());
    }

    public function test_it_returns_group_id_if_response_is_successful()
    {
        $xmlApiResponse = new JetSmsXmlResponse('00 123');

        $this->assertEquals('123', $xmlApiResponse->groupId());
    }

    public function test_it_shouts_out_if_the_http_api_message_identifiers_are_requested()
    {
        $httpApiResponse = new JetSmsXmlResponse("00 123");

        $e = null;
        try {
            $httpApiResponse->messageReportIdentifiers();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(\BadMethodCallException::class, $e);
    }

    public function test_it_returns_status_code()
    {
        $xmlApiResponse = new JetSmsXmlResponse('00 123');

        $this->assertEquals('00', $xmlApiResponse->statusCode());
    }

    public function test_it_returns_status()
    {
        $xmlApiResponse = new JetSmsXmlResponse('00 123');

        $this->assertEquals('Success', $xmlApiResponse->status());
    }
}
