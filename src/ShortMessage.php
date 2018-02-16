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
     * @param string       $body
     * @param string|array $receivers
     */
    public function __construct($receivers, $body)
    {
        $this->body = $body;
        $this->receivers = is_array($receivers)
            ? array_map('trim', $receivers)
            : [trim($receivers)];
    }

    public function body()
    {
        return $this->body;
    }

    public function hasManyReceivers()
    {
        return count($this->receivers()) > 1;
    }

    public function receivers()
    {
        return $this->receivers;
    }

    public function receiversString($glue = null)
    {
        return implode($glue, $this->receivers);
    }

    public function toArray()
    {
        return array_filter([
            'Msisdns'        => $this->receiversString('|'),
            'Messages'       => $this->body(),
        ]);
    }

    public function toSingleMessageXml()
    {
        $text = str_replace("'", "&apos;", htmlentities($this->body()));
        $gsmNo = $this->receiversString(',');

        return "<text>{$text}</text><message><gsmnos>{$gsmNo}</gsmnos></message>";
    }

    /**
     * Get the xml representation of the short message.
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