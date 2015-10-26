<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Output;

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
     * @var bool
     */
    private $quiet;

    /**
     * @param OutputInterface $output
     * @param StopWatch       $stopwatch
     * @param bool            $quiet
     */
    public function __construct(OutputInterface $output, Stopwatch $stopwatch, $quiet)
    {
        $this->output = $output;
        $this->stopwatch = $stopwatch;
        $this->quiet = $quiet;
    }

    public function startProgress()
    {
        if ($this->quiet) {
            return;
        }

        $this->stopwatch->start(static::PROGRESS_NAME);
        $this->output->writeln('Checking your application for deprecations - this could take a while ...');
    }

    public function endProgress($fileCount, $violationCount)
    {
        if ($this->quiet) {
            return;
        }

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
        if ($this->quiet) {
            return;
        }

        $this->output->writeln('Loading RuleSet...');
        $this->output->writeln('');
    }

    public function endRuleSetGeneration()
    {
        if ($this->quiet) {
            return;
        }

        $this->output->writeln('RuleSet loaded.');
    }

    public function startUsageDetection()
    {
        if ($this->quiet) {
            return;
        }

        $this->output->writeln('Searching for deprecations...');
        $this->output->writeln('');
    }

    public function endUsageDetection()
    {
        if ($this->quiet) {
            return;
        }

        $this->output->writeln('Finished searching for deprecations.');
    }

    public function startRendering()
    {
        if ($this->quiet) {
            return;
        }

        $this->output->writeln('Rendering output...');
        $this->output->writeln('');
    }

    public function endRendering()
    {
        if ($this->quiet) {
            return;
        }

        $this->output->writeln('Finished rendering output.');
    }
}
