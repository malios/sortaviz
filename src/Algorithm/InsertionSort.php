<?php declare(strict_types=1);

namespace Malios\Sortavis\Algorithm;

use Malios\Sortavis\Collection;

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
            $this->trigger('select.index', $i);
            for ($j = $i; $j > 0; $j--) {
                $this->trigger('check.lt', [$j, $j - 1]);
                if (!$collection->lessThan($j, $j - 1)) {
                    break;
                }
                $this->trigger('pre.swap', [$j, $j - 1]);
                $collection->swap($j, $j - 1);
                $this->trigger('post.swap', [$j, $j - 1]);
            }
            $this->trigger('deselect.index', $i);
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
