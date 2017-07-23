<?php declare(strict_types=1);

namespace Malios\Sortaviz;

interface Observable
{
    /**
     * Attach event listener for an event
     *
     * @param string $eventName
     * @param callable $listener
     * @return mixed
     */
    public function listen(string $eventName, callable $listener);

    /**
     * Trigger an event
     *
     * @param string $eventName
     * @param $data
     * @return mixed
     */
    public function trigger(string $eventName, $data = null);
}
