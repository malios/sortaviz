<?php declare(strict_types=1);

namespace Malios\Sortavis\Console;

use Malios\Sortavis\Collection;
use Malios\Sortavis\Sort;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class VisualizeCommand extends Command
{
    const NAME = 'visualize';

    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Visualise sorting algorithm')
            ->addOption('algo', 'a', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $algo = $input->getOption('algo');
        if (false) { // todo
            $output->writeln(sprintf('<error>Wrong algorithm %s</error>', $algo));
            die(1);
        }

        $inputString = $this->prompt($input, $output, 'Please enter the numbers (comma separated): ');
        $numbers = array_map(function ($numAsString) use ($output) {
            if (!is_numeric($numAsString)) {
                $output->writeln('<error>Wrong format</error>');
                die(1);
            }

            return (float)$numAsString;
        }, explode(',', $inputString));

        $iterations = 0;
        $collection = new Collection(...$numbers);
        $collection->listen('check.lt', function ($data) use (&$iterations, $output, $collection) {
            $iterations++;
            $numbers = $collection->toArray();
            $output->writeln("<info>Checking if {$numbers[$data[0]]} < {$numbers[$data[1]]}</info>");
        });

        $collection->listen('post.swap', function ($data) use ($output, $collection) {
            $numbers = $collection->toArray();
            $output->writeln("<info>Swapping {$numbers[$data[0]]} with {$numbers[$data[1]]}</info>");
        });

        Sort::bubbleSort($collection);
        $output->writeln("Result: [" . join(', ', $collection->toArray()) . "]");
        $output->writeln("<comment>Iterations: $iterations</comment>");

    }

    private function prompt(InputInterface $input, OutputInterface $output, $promptString) : string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $question = new Question('<question>' . $promptString . '</question>');

        return (string) $questionHelper->ask($input, $output, $question);
    }
}
