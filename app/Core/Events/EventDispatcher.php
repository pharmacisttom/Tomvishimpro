<?php

namespace Tomvis\HIMPro\Core\Events;

use Closure;

class EventDispatcher
{
    protected array $listeners = [];

    public function listen(string $eventName, Closure $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(Event $event): void
    {
        $eventName = $event->getName();
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $listener($event);
            }
        }
    }
}
