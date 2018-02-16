<?php

namespace Erdemkeren\JetSms;

/**
 * Class ShortMessageFactoryInterface.
 */
interface ShortMessageFactoryInterface
{
    /**
     * Create a new short message instance with the given properties.
     *
     * @param  string|array $receivers
     * @param  string       $body
     *
     * @return ShortMessage
     */
    public function create($receivers, $body);
}
