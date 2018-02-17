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
     * The before callback which will be called before sending single messages.
     *
     * @var callable|null
     */
    private $beforeSingleShortMessageCallback;

    /**
     * The after callback which will be called before sending single messages.
     *
     * @var callable|null
     */
    private $afterSingleShortMessageCallback;

    /**
     * The before callback which will be called before sending multiple messages.
     *
     * @var callable|null
     */
    private $beforeMultipleShortMessageCallback;

    /**
     * The after callback which will be called after sending multiple messages.
     *
     * @var callable|null
     */
    private $afterMultipleShortMessageCallback;

    /**
     * JetSmsService constructor.
     *
     * @param  JetSmsClientInterface                  $jetSmsClient
     * @param  ShortMessageFactoryInterface           $shortMessageFactory
     * @param  ShortMessageCollectionFactoryInterface $shortMessageCollectionFactory
     * @param  callable|null                          $beforeSingleShortMessageCallback
     * @param  callable|null                          $afterSingleShortMessageCallback
     * @param  callable|null                          $beforeMultipleShortMessageCallback
     * @param  callable|null                          $afterMultipleShortMessageCallback
     */
    public function __construct(
        JetSmsClientInterface $jetSmsClient,
        ShortMessageFactoryInterface $shortMessageFactory,
        ShortMessageCollectionFactoryInterface $shortMessageCollectionFactory,
        $beforeSingleShortMessageCallback = null,
        $afterSingleShortMessageCallback = null,
        $beforeMultipleShortMessageCallback = null,
        $afterMultipleShortMessageCallback = null
    ) {
        $this->client = $jetSmsClient;
        $this->factory = $shortMessageFactory;
        $this->collectionFactory = $shortMessageCollectionFactory;
        $this->beforeSingleShortMessageCallback = $beforeSingleShortMessageCallback;
        $this->afterSingleShortMessageCallback = $afterSingleShortMessageCallback;
        $this->beforeMultipleShortMessageCallback = $beforeMultipleShortMessageCallback;
        $this->afterMultipleShortMessageCallback = $afterMultipleShortMessageCallback;
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

        if (is_callable($this->beforeSingleShortMessageCallback)) {
            call_user_func_array($this->beforeSingleShortMessageCallback, [$shortMessage]);
        }

        $response = $this->client->sendShortMessage($shortMessage);

        if (is_callable($this->afterSingleShortMessageCallback)) {
            call_user_func_array($this->afterSingleShortMessageCallback, [$response, $shortMessage]);
        }

        return $response;
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

        if (is_callable($this->beforeMultipleShortMessageCallback)) {
            call_user_func_array($this->beforeMultipleShortMessageCallback, [$collection]);
        }

        foreach ($messages as $message) {
            $collection->push($this->factory->create(
                $message['recipient'],
                $message['message']
            ));
        }

        $response = $this->client->sendShortMessages($collection);

        if (is_callable($this->afterMultipleShortMessageCallback)) {
            call_user_func_array($this->afterMultipleShortMessageCallback, [$response, $collection]);
        }

        return $response;
    }
}
