<?php declare(strict_types=1);

namespace Malios\Sortavis;

class Sort
{
    /**
     * @param Collection $collection
     * @link https://upload.wikimedia.org/wikipedia/commons/5/54/Sorting_bubblesort_anim.gif
     */
    public static function bubbleSort(Collection $collection)
    {
        $len = $collection->count();
        for ($i = 0; $i < $len; $i++) {
            for ($j = 0; $j < $len - $i - 1; $j++) {
                if ($collection->lessThan($j + 1, $j)) {
                    $collection->swap($j, $j + 1);
                }
            }
        }
    }
}
