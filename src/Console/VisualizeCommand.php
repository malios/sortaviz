<?php declare(strict_types=1);

namespace Malios\Sortavis\Console;

use Malios\Sortavis\Algorithm\BubbleSort;
use Malios\Sortavis\Algorithm\InsertionSort;
use Malios\Sortavis\Algorithm\SelectionSort;
use Malios\Sortavis\Collection;
use Malios\Sortavis\Visualizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class VisualizeCommand extends Command
{
    const NAME = 'visualize';
    const DEFAULT_SPEED = 1;

    private $algorithms = [
        'bubblesort' => BubbleSort::class,
        'selectionsort' => SelectionSort::class,
        'insertionsort' => InsertionSort::class,
    ];

    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Visualise sorting algorithm')
            ->addOption('algo', 'a', InputOption::VALUE_REQUIRED)
            ->addOption('speed', 's', InputOption::VALUE_OPTIONAL, 'Positive number');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $algoName = $input->getOption('algo');
        if (!isset($this->algorithms[$algoName])) { // todo
            $output->writeln(sprintf(
                "<error>Wrong algorithm %s \nAvailable options are [%s]</error>",
                $algoName,
                join(', ', array_keys($this->algorithms))
            ));
            die(1);
        }

        $speedOption = $input->getOption('speed');
        $speed = $speedOption ? (float) $speedOption : self::DEFAULT_SPEED;
        if ($speed <= 0) {
            $output->writeln("<error>Speed must be a positive number</error>");
            die(1);
        }

        $inputString = $this->prompt($input, $output, 'Please enter the numbers (comma separated): ');
        try {
            $numbers = $this->parseNumbers($inputString);
        } catch (\InvalidArgumentException $iae) {
            $output->writeln('<error>Wrong format</error>');
            die(1);
        }

        $collection = new Collection(...$numbers);
        $algo = new $this->algorithms[$algoName]();
        $visualizer = new Visualizer($output, $collection, $algo, (int) (1000 / $speed));
        $visualizer->visualizeOnChange();

        $algo($collection);

        sleep(11111); // so the user can have time to analyze the results
    }

    /**
     * @param string $userInput
     * @return float[]
     */
    private function parseNumbers(string $userInput) : array
    {
        $numbers = array_map(function ($numAsString) {
            if (!is_numeric($numAsString)) {
                throw new \InvalidArgumentException('Invalid input string');
            }

            return (float)$numAsString;
        }, explode(',', $userInput));

        return $numbers;
    }

    private function prompt(InputInterface $input, OutputInterface $output, $promptString) : string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $question = new Question('<question>' . $promptString . '</question>');

        return (string) $questionHelper->ask($input, $output, $question);
    }
}
