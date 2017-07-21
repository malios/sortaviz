<?php declare(strict_types=1);

namespace Malios\Sortavis\Algorithm;

use Malios\Sortavis\Collection;
use Malios\Sortavis\Observable;

abstract class Algorithm implements Observable
{
    private $listeners = [];

    /**
     * {@inheritdoc}
     * @see Observable::listen()
     */
    public function listen(string $eventName, callable $listener)
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * {@inheritdoc}
     * @see Observable::trigger()
     */
    public function trigger(string $eventName, $data = null)
    {
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $listener($data);
            }
        }
    }

    /**
     * The implementation of the concrete algorithm
     *
     * @param Collection $collection
     * @return void
     */
    abstract public function __invoke(Collection $collection);

    abstract public function getName() : string;
}
