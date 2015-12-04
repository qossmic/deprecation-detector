<?php

namespace SensioLabs\DeprecationDetector\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class DefaultProgressOutput
{
    const PROGRESS_NAME = 'check';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @param OutputInterface $output
     * @param StopWatch       $stopwatch
     */
    public function __construct(OutputInterface $output, Stopwatch $stopwatch)
    {
        $this->output = $output;
        $this->stopwatch = $stopwatch;
    }

    public function startProgress()
    {
        $this->stopwatch->start(static::PROGRESS_NAME);
        $this->output->writeln('Checking your application for deprecations - this could take a while ...'.PHP_EOL);
    }

    /**
     * @param int $fileCount
     * @param int $violationCount
     */
    public function endProgress($fileCount, $violationCount)
    {
        $stats = $this->stopwatch->stop(static::PROGRESS_NAME);
        if (0 === $violationCount) {
            $this->output->writeln('<info>There are no violations - congratulations!</info>');
        } else {
            $this->output->writeln(
                sprintf(
                    '<comment>%s deprecations found.</comment>',
                    $violationCount
                )
            );
        }

        $this->output->writeln(
            sprintf(
                'Checked %s source files in %s seconds, %s MB memory used',
                $fileCount,
                $stats->getDuration() / 1000,
                $stats->getMemory() / 1024 / 1024
            )
        );
    }

    public function startRuleSetGeneration()
    {
        $this->output->writeln('Loading RuleSet...'.PHP_EOL);
    }

    public function endRuleSetGeneration()
    {
        $this->output->writeln('RuleSet loaded.'.PHP_EOL);
    }

    public function startUsageDetection()
    {
        $this->output->writeln('Parsing files & Searching for deprecations...'.PHP_EOL);
    }

    public function endUsageDetection()
    {
        $this->output->writeln('Finished searching for deprecations.'.PHP_EOL);
    }

    public function startOutputRendering()
    {
        $this->output->writeln('Rendering output...'.PHP_EOL);
    }

    public function endOutputRendering()
    {
        $this->output->writeln('Finished rendering output.'.PHP_EOL);
    }
}
