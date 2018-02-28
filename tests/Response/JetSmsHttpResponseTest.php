<?php

namespace Erdemkeren\JetSms\Test;

use PHPUnit\Framework\TestCase;
use Erdemkeren\JetSms\Http\Responses\JetSmsHttpResponse;

class JetSmsHttpResponseTest extends TestCase
{
    public function test_it_returns_true_if_the_response_is_successful()
    {
        $httpApiResponse = new JetSmsHttpResponse("Status=0\r\nMessageIDs=151103141334228\r\nType=Fake\r\n");

        $this->assertTrue($httpApiResponse->isSuccessful());
    }

    public function test_it_returns_null_error_message_if_the_response_is_successful()
    {
        $httpApiResponse = new JetSmsHttpResponse("Status=0\r\nMessageIDs=151103141334228\r\nType=Fake\r\n");

        $this->assertNull($httpApiResponse->message());
    }

    public function test_it_returns_the_status_code_of_the_response()
    {
        $httpApiResponse = new JetSmsHttpResponse("Status=0\r\nMessageIDs=151103141334228\r\nType=Fake\r\n");

        $this->assertEquals('0', $httpApiResponse->statusCode());
    }

    public function test_it_returns_the_status_message_of_the_response()
    {
        $httpApiResponse = new JetSmsHttpResponse("Status=-5\r\nType=Fake\r\n");

        $this->assertEquals('The SMS service credentials are incorrect', $httpApiResponse->status());
    }

    public function test_it_returns_empty_array_if_no_message_report_identifiers_returned()
    {
        $httpApiResponse = new JetSmsHttpResponse("Status=-5\r\nType=Fake\r\n");

        $this->assertEquals([], $httpApiResponse->messageReportIdentifiers());
    }

    public function test_it_returns_message_report_identifiers()
    {
        $httpApiResponse = new JetSmsHttpResponse("Status=0\r\nMessageIDs=151103141334228|151103141334229\r\nType=Fake\r\n");

        $this->assertEquals([
            '151103141334228',
            '151103141334229',
        ], $httpApiResponse->messageReportIdentifiers());
    }

    public function test_it_shouts_out_if_the_xml_api_group_id_requested()
    {
        $httpApiResponse = new JetSmsHttpResponse("Status=0\r\nMessageIDs=151103141334228|151103141334229\r\nType=Fake\r\n");

        $e = null;
        try {
            $httpApiResponse->groupId();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(\BadMethodCallException::class, $e);
    }
}
