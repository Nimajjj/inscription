<?php

namespace App\Event;


final class EventManager
{
    private array $listeners = [];

    public function subscribe(IEvent $event, callable $listener): EventManager
    {
        echo("[EventManager] Subscribing to event: " . $event->name()) . "\n";

        if (!isset($this->listeners[$event->name()]))
        {
            $this->listeners[$event->name()] = [];
        }
        $this->listeners[$event->name()][] = $listener;

        return $this;
    }

    public function unsubscribe(IEvent $event, callable $listener): void
    {
        echo("[EventManager] Unsubscribing from event: " . $event->name()) . "\n";

        if (isset($this->listeners[$event->name()]))
        {
            $beforeCount = count($this->listeners[$event->name()]);
            $this->listeners[$event->name()] = array_filter(
                $this->listeners[$event->name()],
                fn($l) => $l !== $listener
            );
            $afterCount = count($this->listeners[$event->name()]);
            echo("[EventManager] Listeners before: $beforeCount, after: $afterCount") . "\n";
        }
    }

    public function notify(IEvent $event): void
    {
        echo("[EventManager] Notifying listeners for event: " . $event->name()) . "\n";

        if (isset($this->listeners[$event->name()]))
        {
            foreach ($this->listeners[$event->name()] as $listener)
            {
                echo("[EventManager] Executing listener for event: " . $event->name()) . "\n";
                $listener($event);
            }
        }
        else
        {
            echo("[EventManager] No listeners found for event: " . $event->name()) . "\n";
        }
    }
}
