<?php declare(strict_types=1);

namespace Malios\Sortavis\Algorithm;

use Malios\Sortavis\Collection;

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
            $this->trigger('select.index', $i);
            for ($j = $i + 1; $j < $len; $j++) {
                $this->trigger('check.lt', [$j, $min]);
                if ($collection->lessThan($j, $min)) {
                    $min = $j;
                    $this->trigger('select.index', $i);
                }
            }

            $this->trigger('pre.swap', [$min, $i]);
            $collection->swap($min, $i);
            $this->trigger('post.swap', [$min, $i]);
        }
        $this->trigger('finish');
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
