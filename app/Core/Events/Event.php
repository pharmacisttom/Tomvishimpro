<?php

namespace Tomvis\HIMPro\Core\Events;

abstract class Event
{
    protected string $eventName;
    protected float $timestamp;

    public function __construct()
    {
        $this->eventName = static::class;
        $this->timestamp = microtime(true);
    }

    public function getName(): string
    {
        return $this->eventName;
    }

    public function getTimestamp(): float
    {
        return $this->timestamp;
    }
}
