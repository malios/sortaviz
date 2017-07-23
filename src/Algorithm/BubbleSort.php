<?php declare(strict_types=1);

namespace Malios\Sortaviz\Algorithm;

use Malios\Sortaviz\Collection;

class BubbleSort extends Algorithm
{
    const NAME = 'Bubble Sort';

    /**
     * {@inheritdoc}
     * @see Algorithm::__invoke()
     * @link https://upload.wikimedia.org/wikipedia/commons/5/54/Sorting_bubblesort_anim.gif
     */
    public function __invoke(Collection $collection)
    {
        $len = $collection->count();
        for ($i = 0; $i < $len; $i++) {
            for ($j = 0; $j < $len - $i - 1; $j++) {
                $this->trigger(Event::CHECK_LESS_THAN, [$j + 1, $j]);
                if ($collection->lessThan($j + 1, $j)) {
                    $this->trigger(Event::PRE_SWAP, [$j, $j + 1]);
                    $collection->swap($j, $j + 1);
                    $this->trigger(Event::POST_SWAP, [$j, $j + 1]);
                }
            }
        }
        $this->trigger(Event::FINISH);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
