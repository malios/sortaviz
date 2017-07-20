<?php declare(strict_types=1);

namespace Malios\Sortavis;

use Symfony\Component\Console\Output\OutputInterface;

class Visualizer
{
    private $collection;
    private $output;
    private $pauseInMicroseconds;
    private $iterations = 0;
    private $operation = '';
    private $numbersOutput = '';
    private $algorithm = '';
    private $styles = [
        'currentCheck' => '<fg=black;bg=blue>%s</>',
        'swapX' => '<fg=black;bg=green>%s</>',
        'swapY' => '<fg=black;bg=red>%s</>'
    ];

    private $template = <<<EOD
Iteration: %s | Operation: %s ? | Algorithm: %s

[%s]

EOD;


    public function __construct(
        OutputInterface $output,
        Collection $collection,
        string $algorithm,
        $pauseInMilliseconds = 300
    ) {
        $this->collection = $collection;
        $this->output = $output;
        $this->pauseInMicroseconds = $pauseInMilliseconds * 1000;
        $this->algorithm = $algorithm;
        $this->numbersOutput = join(', ', $collection->toArray());
    }

    public function visualizeOnChange()
    {
        $this->iterations = 0;
        $this->collection->listen('check.lt', function ($data) {
            $this->clear();

            $numbers = $this->collection->toArray();
            $this->operation = "{$numbers[$data[0]]} < {$numbers[$data[1]]}";
            $this->iterations++;
            $this->numbersOutput = $this->join($numbers, ', ', function ($i, $v) use ($data) {
                if ($i === $data[0] || $i === $data[1]) {
                    return sprintf($this->styles['currentCheck'], $v);
                }

                return $v;
            });

            $this->updateView();
            $this->pause();
        });

        $this->collection->listen('pre.swap', function ($data) {
            $this->clear();

            $numbers = $this->collection->toArray();
            $this->numbersOutput = $this->join($numbers, ', ', function ($i, $v) use ($data) {
                if ($i === $data[0]) {
                    return sprintf($this->styles['swapX'], $v);
                }

                if ($i === $data[1]) {
                    return sprintf($this->styles['swapY'], $v);
                }

                return $v;
            });

            $this->updateView();
            $this->pause();
        });

        $this->collection->listen('post.swap', function ($data) {
            $this->clear();

            $numbers = $this->collection->toArray();
            $this->numbersOutput = $this->join($numbers, ', ', function ($i, $v) use ($data) {
                if ($i === $data[0]) {
                    return sprintf($this->styles['swapY'], $v);
                }

                if ($i === $data[1]) {
                    return sprintf($this->styles['swapX'], $v);
                }

                return $v;
            });

            $this->updateView();
            $this->pause();
        });

        $this->collection->listen('finish', function () {
            $this->numbersOutput = join(', ', $this->collection->toArray());
            $this->clear();
            $this->updateView();
            $this->output->writeln('Finished...');
        });
    }

    public function updateView()
    {
        $this->output->writeln(sprintf(
            $this->template,
            $this->iterations,
            $this->operation,
            $this->algorithm,
            $this->numbersOutput
        ));
    }

    public function pause()
    {
        usleep($this->pauseInMicroseconds);
    }

    public function clear()
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
    public function join(array $arr, $glue = "", callable $modifierFunc = null)
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
