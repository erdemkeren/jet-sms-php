<?php

namespace Erdemkeren\JetSms;

/**
 * Class ShortMessage.
 */
class ShortMessage
{
    /**
     * The short message body.
     *
     * @var string
     */
    protected $body;

    /**
     * The receivers.
     *
     * @var array
     */
    protected $receivers;

    /**
     * ShortMessage constructor.
     *
     * @param string|array $receivers
     * @param string       $body
     */
    public function __construct($receivers, $body)
    {
        $this->body = $body;
        $this->receivers = is_array($receivers)
            ? array_map('trim', $receivers)
            : [trim($receivers)];
    }

    /**
     * Get the body of the short message.
     *
     * @return string
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * Determine if the short message has many receivers or not.
     *
     * @return bool
     */
    public function hasManyReceivers()
    {
        return count($this->receivers()) > 1;
    }

    /**
     * Get the receivers of the short message.
     *
     * @return array
     */
    public function receivers()
    {
        return $this->receivers;
    }

    /**
     * Get the receivers of the short message as concatenated string.
     *
     * @param  string|null $glue
     * @return string
     */
    public function receiversString($glue = null)
    {
        return implode($glue, $this->receivers);
    }

    /**
     * Get the array representation of the short message.
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'Msisdns'        => $this->receiversString('|'),
            'Messages'       => $this->body(),
        ]);
    }

    /**
     * Get the single message xml representation of the short message.
     *
     * @return string
     */
    public function toSingleMessageXml()
    {
        $text = str_replace("'", "&apos;", htmlentities($this->body()));
        $gsmNo = $this->receiversString(',');

        return "<text>{$text}</text><message><gsmnos>{$gsmNo}</gsmnos></message>";
    }

    /**
     * Get the multiple messages xml representation of the short message.
     *
     * @return string
     */
    public function toMultipleMessagesXml()
    {
        $text = str_replace("'", "&apos;", htmlentities($this->body()));
        $gsmNo = $this->receiversString(',');

        return "<message><gsmno>{$gsmNo}</gsmno><text>{$text}</text></message>";
    }
}
