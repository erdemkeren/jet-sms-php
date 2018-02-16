<?php

namespace Erdemkeren\JetSms;

/**
 * Interface ShortMessageCollectionFactoryInterface.
 */
interface ShortMessageCollectionFactoryInterface
{
    /**
     * Create a new short message collection.
     *
     * @return ShortMessageCollection
     */
    public function create();
}
