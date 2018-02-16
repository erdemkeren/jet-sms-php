<?php

namespace Erdemkeren\JetSms;

/**
 * Class ShortMessageCollection.
 */
class ShortMessageCollection
{
    /**
     * The items of the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Push a new short message to the given collection.
     *
     * @param ShortMessage $shortMessage
     *
     * @return self
     */
    public function push(ShortMessage $shortMessage)
    {
        if($shortMessage->hasManyReceivers()) {
            throw new \LogicException(
                "Expected one receiver per short message, got many."
            );
        }

        $this->items[] = $shortMessage;

        return $this;
    }

    /**
     * Get the xml representation of the items.
     *
     * @return string
     */
    public function toXml()
    {
        $messages = '';
        /** @var ShortMessage $shortMessage */
        foreach ($this->items as $shortMessage) {
            $messages .= $shortMessage->toMultipleMessagesXml();
        }

        return $messages;
    }

    /**
     * Get the array presentation of the items.
     *
     * @return array
     */
    public function toArray()
    {
        $messages = [];
        $receivers = [];

        /** @var ShortMessage $shortMessage */
        foreach ($this->items as $shortMessage) {
            $messages[] = $shortMessage->body();
            $receivers[] = $shortMessage->receiversString();
        }

        return [
            'Msisdns'        => implode('|', $receivers),
            'Messages'       => implode('|', $messages),
        ];
    }
}