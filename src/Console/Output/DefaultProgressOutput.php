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
        $this->output->writeln('Checking your application for deprecations - this could take a while ...');
    }

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
        $this->output->writeln('Loading RuleSet...');
    }

    public function endRuleSetGeneration()
    {
        $this->output->writeln('RuleSet loaded.');
    }

    public function startUsageDetection()
    {
        $this->output->writeln('Searching for deprecations...');
    }

    public function endUsageDetection()
    {
        $this->output->writeln('Finished searching for deprecations.');
    }

    public function startOutputRendering()
    {
        $this->output->writeln('Rendering output...');
    }

    public function endOutputRendering()
    {
        $this->output->writeln('Finished rendering output.');
    }
}
