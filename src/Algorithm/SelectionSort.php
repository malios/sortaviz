<?php declare(strict_types=1);

namespace Malios\Sortaviz\Algorithm;

use Malios\Sortaviz\Collection;

class SelectionSort extends Algorithm
{
    const NAME = 'Selection Sort';

    /**
     * {@inheritdoc}
     * @see Algorithm::__invoke()
     * @link https://upload.wikimedia.org/wikipedia/commons/9/94/Selection-Sort-Animation.gif
     */
    public function __invoke(Collection $collection)
    {
        $len = $collection->count();
        for ($i = 0; $i < $len - 1; $i++) {
            $min = $i;
            $this->trigger(Event::SELECT_INDEX, $i);
            for ($j = $i + 1; $j < $len; $j++) {
                $this->trigger(Event::CHECK_LESS_THAN, [$j, $min]);
                if ($collection->lessThan($j, $min)) {
                    $min = $j;
                    $this->trigger(Event::SELECT_INDEX, $i);
                }
            }

            $this->trigger(Event::PRE_SWAP, [$min, $i]);
            $collection->swap($min, $i);
            $this->trigger(Event::POST_SWAP, [$min, $i]);
        }
        $this->trigger(Event::FINISH);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
