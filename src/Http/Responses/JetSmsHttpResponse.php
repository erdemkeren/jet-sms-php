<?php

namespace Erdemkeren\JetSms\Http\Responses;

use BadMethodCallException;

/**
 * Class JetSmsHttpResponse.
 */
final class JetSmsHttpResponse implements JetSmsResponseInterface
{
    /**
     * The read response of SMS message request..
     *
     * @var array
     */
    private $responseAttributes = [];

    /**
     * The JetSMS error codes.
     *
     * @var array
     */
    private static $statuses = [
        '0'   => 'Success',
        '-1'  => 'The specified SMS outbox name is invalid',
        '-5'  => 'The SMS service credentials are incorrect',
        '-6'  => 'The specified data is malformed',
        '-7'  => 'The send date of the SMS message has already expired',
        '-8'  => 'The SMS gsm number is invalid',
        '-9'  => 'The SMS message body is missing',
        '-15' => 'The SMS service is having some trouble at the moment',
        '-99' => 'The SMS service encountered an unexpected error',
    ];

    /**
     * Create a message response.
     *
     * @param  string $responseBody
     */
    public function __construct($responseBody)
    {
        $this->responseAttributes = $this->readResponseBodyString($responseBody);
    }

    /**
     * Determine if the operation was successful or not.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return '0' === $this->statusCode();
    }

    /**
     * Get the status code.
     *
     * @return string
     */
    public function statusCode()
    {
        return (string) $this->responseAttributes['statusCode'];
    }

    /**
     * Get the string representation of the status.
     *
     * @return string
     */
    public function status()
    {
        return array_key_exists($this->statusCode(), self::$statuses)
            ? self::$statuses[$this->statusCode()]
            : 'Unknown';
    }

    /**
     * Get the message of the response.
     *
     * @return null|string
     */
    public function message()
    {
        return null;
    }

    /**
     * Get the group identifier from the response.
     */
    public function groupId()
    {
        throw new BadMethodCallException(
            "JetSms Http API responses do not group bulk message identifiers. Use messageReportIdentifiers instead."
        );
    }

    /**
     * Get the message report identifiers for the messages sent.
     * Message report id returns -1 if invalid Msisdns, -2 if invalid message text.
     *
     * @return array
     */
    public function messageReportIdentifiers()
    {
        if (array_key_exists('messageids', $this->responseAttributes)) {
            return explode('|', $this->responseAttributes['messageids']);
        }

        return [];
    }

    /**
     * Read the message response body string.
     *
     * @param $responseBodyString
     * @return array
     */
    private function readResponseBodyString($responseBodyString)
    {
        $responseLines = array_filter(array_map(function ($value) {
            return trim($value);
        }, explode("\n", $responseBodyString)));

        $result = [];
        foreach ($responseLines as $responseLine) {
            $responseParts = explode('=', $responseLine);
            $result[strtolower($responseParts[0])] = $responseParts[1];
        }

        $status = (int) $result['status'];
        unset($result['status']);
        $result['success'] = ($status >= 0) ? true : false;
        $result['statusCode'] = $status;

        return $result;
    }
}
