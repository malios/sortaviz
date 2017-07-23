<?php declare(strict_types=1);

namespace Malios\Sortaviz\Algorithm;

use Malios\Sortaviz\Collection;

class InsertionSort extends Algorithm
{
    const NAME = 'Insertion Sort';

    /**
     * {@inheritdoc}
     * @see Algorithm::__invoke()
     * @link https://upload.wikimedia.org/wikipedia/commons/0/0f/Insertion-sort-example-300px.gif
     */
    public function __invoke(Collection $collection)
    {
        $len = $collection->count();
        for ($i = 0; $i < $len; $i++) {
            $this->trigger(Event::SELECT_INDEX, $i);
            for ($j = $i; $j > 0; $j--) {
                $this->trigger(Event::CHECK_LESS_THAN, [$j, $j - 1]);
                if (!$collection->lessThan($j, $j - 1)) {
                    break;
                }
                $this->trigger(Event::PRE_SWAP, [$j, $j - 1]);
                $collection->swap($j, $j - 1);
                $this->trigger(Event::POST_SWAP, [$j, $j - 1]);
            }
            $this->trigger(Event::DESELECT_INDEX, $i);
        }
        $this->trigger(Event::FINISH);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
