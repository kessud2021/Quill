<?php

namespace Framework\Events;

class Dispatcher {
    protected $listeners = [];

    public function listen($event, $listener) {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    public function dispatch($event, $data = null) {
        $eventName = is_string($event) ? $event : get_class($event);

        if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $listener) {
            if (is_string($listener)) {
                $listener = app($listener);
            }

            if (is_callable($listener)) {
                call_user_func($listener, $data ?? $event);
            } elseif (method_exists($listener, 'handle')) {
                $listener->handle($data ?? $event);
            }
        }
    }

    public function hasListeners($event) {
        return isset($this->listeners[$event]);
    }

    public function getListeners($event) {
        return $this->listeners[$event] ?? [];
    }
}
