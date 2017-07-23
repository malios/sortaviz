<?php declare(strict_types=1);

namespace Malios\Sortavis;

use Malios\Sortavis\Algorithm\Algorithm;
use Symfony\Component\Console\Output\OutputInterface;

class Visualizer
{
    private $output;
    private $algorithm;
    private $collection;
    private $pauseInMicroseconds;
    private $iterations = 0;
    private $operation = '';
    private $swapIndexes = [];
    private $comparedIndexes = [];
    private $selectedIndexes = [];
    private $styles = [
        'currentCheck' => '<fg=black;bg=blue>%s</>',
        'swapX' => '<fg=black;bg=green>%s</>',
        'swapY' => '<fg=black;bg=red>%s</>',
        'selected' => '<fg=black;bg=yellow>%s</>',
    ];

    private $template = <<<EOD
Algorithm: %s | Iteration: %s | Operation: %s

[%s]

EOD;

    public function __construct(
        OutputInterface $output,
        Collection $collection,
        Algorithm $algorithm,
        $pauseInMilliseconds = 300
    ) {
        $this->collection = $collection;
        $this->output = $output;
        $this->pauseInMicroseconds = $pauseInMilliseconds * 1000;
        $this->algorithm = $algorithm;
    }

    public function visualizeOnChange()
    {
        $this->iterations = 0;
        $this->algorithm->listen('check.lt', function (array $data) {
            $this->clear();

            $this->iterations++;
            $numbers = $this->collection->toArray();
            $this->operation = "{$numbers[$data[0]]} < {$numbers[$data[1]]}?";
            $this->comparedIndexes = $data;

            $this->updateView();
            $this->comparedIndexes = [];
            $this->operation = '';

            $this->pause();
        });

        $this->algorithm->listen('pre.swap', function (array $data) {
            $this->clear();
            $this->operation = "swap";
            $this->swapIndexes = $data;
            $this->updateView();
            $this->swapIndexes = [];
            $this->pause();
        });

        $this->algorithm->listen('post.swap', function (array $data) {
            $this->clear();
            $this->swapIndexes = [$data[1], $data[0]];
            $this->updateView();
            $this->operation = '';
            $this->swapIndexes = [];
            $this->pause();
        });

        $this->algorithm->listen('select.index', function (int $index) {
            $this->clear();
            $this->selectedIndexes[] = $index;
            $this->updateView();
            $this->pause();
        });

        $this->algorithm->listen('deselect.index', function (int $index) {
            $this->clear();
            $this->selectedIndexes = array_filter($this->selectedIndexes, function ($v) use ($index) {
                return $v !== $index;
            });
            $this->updateView();
            $this->pause();
        });

        $this->algorithm->listen('finish', function () {
            $this->selectedIndexes = [];
            $this->clear();
            $this->updateView();
            $this->output->writeln('Finished...');
        });
    }

    private function updateView()
    {
        $numbers = $this->collection->toArray();
        $numbersOutput = $this->join($numbers, ', ', function ($i, $v) {
            if (count($this->swapIndexes) > 0 && $i === $this->swapIndexes[0]) {
                return sprintf($this->styles['swapX'], $v);
            }

            if (count($this->swapIndexes) > 1 && $i === $this->swapIndexes[1]) {
                return sprintf($this->styles['swapY'], $v);
            }

            if ($this->comparedIndexes
                && ($i === $this->comparedIndexes[0]
                    || $i === $this->comparedIndexes[1])) {
                return sprintf($this->styles['currentCheck'], $v);
            }

            if (in_array($i, $this->selectedIndexes)) {
                return sprintf($this->styles['selected'], $v);
            }

            return $v;
        });

        $this->output->writeln(sprintf(
            $this->template,
            $this->algorithm->getName(),
            $this->iterations,
            $this->operation,
            $numbersOutput
        ));
    }

    private function pause()
    {
        usleep($this->pauseInMicroseconds);
    }

    private function clear()
    {
        // todo: windows
        system('clear');
    }

    /**
     * Custom array join function with modifier function to add styles on the run
     *
     * @param array $arr
     * @param string $glue
     * @param callable $modifierFunc
     * @return string
     */
    private function join(array $arr, $glue = "", callable $modifierFunc = null)
    {
        $result = '';
        $len = count($arr);
        foreach ($arr as $index => $value) {
            if ($modifierFunc) {
                $value = $modifierFunc($index, $value);
            }

            $result .= (string) $value;
            if ($index + 1 < $len) {
                $result .= $glue;
            }
        }

        return $result;
    }
}
