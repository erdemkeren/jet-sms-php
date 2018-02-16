<?php

namespace Erdemkeren\JetSms;

use Erdemkeren\JetSms\Http\Clients\JetSmsClientInterface;
use Erdemkeren\JetSms\Http\Responses\JetSmsResponseInterface;

/**
 * Class JetSmsService.
 */
final class JetSmsService
{
    /**
     * The jet sms client implementation.
     *
     * @var JetSmsClientInterface
     */
    private $client;

    /**
     * The short message factory implementation.
     *
     * @var ShortMessageFactoryInterface
     */
    private $factory;

    /**
     * The short message collection factory implementation.
     *
     * @var ShortMessageFactoryInterface
     */
    private $collectionFactory;

    /**
     * JetSmsService constructor.
     *
     * @param  JetSmsClientInterface        $jetSmsClient
     * @param  ShortMessageFactoryInterface $shortMessageFactory
     * @param  ShortMessageCollectionFactoryInterface $shortMessageCollectionFactory
     */
    public function __construct(
        JetSmsClientInterface $jetSmsClient,
        ShortMessageFactoryInterface $shortMessageFactory,
        ShortMessageCollectionFactoryInterface $shortMessageCollectionFactory
    ) {
        $this->client = $jetSmsClient;
        $this->factory = $shortMessageFactory;
        $this->collectionFactory = $shortMessageCollectionFactory;
    }

    /**
     * Send the given body to the given receivers.
     *
     * @param  string       $body      The body of the short message.
     * @param  array|string $receivers The receiver(s) of the message.
     *
     * @return JetSmsResponseInterface The parsed JetSms response object.
     */
    public function sendShortMessage($receivers, $body)
    {
        $shortMessage = $this->factory->create($receivers, $body);

        return $this->client->sendShortMessage($shortMessage);
    }

    /**
     * Send the given short messages.
     *
     * @param  array $messages         An array containing short message arrays.
     *
     * @return JetSmsResponseInterface The parsed JetSms response object.
     */
    public function sendShortMessages(array $messages)
    {
        $collection = $this->collectionFactory->create();

        foreach ($messages as $message) {
            $collection->push($this->factory->create(
                $message['recipient'],
                $message['message']
            ));
        }

        return $this->client->sendShortMessages($collection);
    }
}
