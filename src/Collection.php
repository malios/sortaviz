<?php declare(strict_types=1);

namespace Malios\Sortaviz;

class Collection
{
    private $values = [];

    public function __construct(float ...$values)
    {
        $this->values = $values;
    }

    public function count() : int
    {
        return count($this->values);
    }

    /**
     * Compare 2 values at given indexes with less than operator
     *
     * @param $i
     * @param $j
     * @return bool
     */
    public function lessThan($i, $j) : bool
    {
        $this->checkIndex($i);
        $this->checkIndex($j);

        return $this->values[$i] < $this->values[$j];
    }

    /**
     * Swap 2 values at given indexes
     *
     * @param int $i
     * @param int $j
     */
    public function swap(int $i, int $j)
    {
        $this->checkIndex($i);
        $this->checkIndex($j);

        $tmp = $this->values[$i];
        $this->values[$i] = $this->values[$j];
        $this->values[$j] = $tmp;
    }

    /**
     * @return float[]
     */
    public function toArray() : array
    {
        return $this->values;
    }

    private function checkIndex(int $index)
    {
        $lastIndex = $this->count() - 1;
        if ($index < 0 || $index > $lastIndex) {
            throw new \OutOfBoundsException(sprintf("Index %s is out of bounds", $index));
        }
    }
}
