<?php

namespace SensioLabs\DeprecationDetector\Tests\Console\Output;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class DefaultProgressOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $output = $this->prophesize(OutputInterface::class);
        $stopwatch = $this->prophesize(Stopwatch::class);

        $defaultProgressOutput = new DefaultProgressOutput(
            $output->reveal(),
            $stopwatch->reveal()
        );

        $this->assertInstanceOf(
            DefaultProgressOutput::class,
            $defaultProgressOutput
        );
    }

    public function testDifferentStages()
    {
        $output = $this->prophesize(OutputInterface::class);
        $output->writeln(Argument::any())->shouldBeCalledTimes(9);
        $stopwatch = $this->prophesize(Stopwatch::class);
        $stopwatch->start(DefaultProgressOutput::PROGRESS_NAME)->shouldBeCalled();
        $stopwatchEvent = $this->prophesize(StopwatchEvent::class);
        $stopwatch
            ->stop(DefaultProgressOutput::PROGRESS_NAME)
            ->willReturn($stopwatchEvent->reveal())
            ->shouldBeCalled($stopwatch);

        $defaultProgressOutput = new DefaultProgressOutput(
            $output->reveal(),
            $stopwatch->reveal()
        );

        $defaultProgressOutput->startProgress();
        $defaultProgressOutput->startRuleSetGeneration();
        $defaultProgressOutput->endRuleSetGeneration();
        $defaultProgressOutput->startUsageDetection();
        $defaultProgressOutput->endUsageDetection();
        $defaultProgressOutput->startOutputRendering();
        $defaultProgressOutput->endOutputRendering();
        $defaultProgressOutput->endProgress(0, 0);
    }

    public function testEndProgressWithoutViolations()
    {
        $output = $this->prophesize(OutputInterface::class);
        $output->writeln(Argument::any())->shouldBeCalledTimes(2);
        $stopwatchEvent = $this->prophesize(StopwatchEvent::class);
        $stopwatch = $this->prophesize(Stopwatch::class);
        $stopwatch
            ->stop(DefaultProgressOutput::PROGRESS_NAME)
            ->willReturn($stopwatchEvent->reveal())
            ->shouldBeCalled($stopwatch);

        $violationCount = 0;
        $fileCount = 10;

        $defaultProgressOutput = new DefaultProgressOutput(
            $output->reveal(),
            $stopwatch->reveal()
        );
        $defaultProgressOutput->endProgress($fileCount, $violationCount);
    }

    public function testEndProgressWithViolations()
    {
        $output = $this->prophesize(OutputInterface::class);
        $output->writeln(Argument::any())->shouldBeCalledTimes(2);
        $stopwatchEvent = $this->prophesize(StopwatchEvent::class);
        $stopwatch = $this->prophesize(Stopwatch::class);
        $stopwatch
            ->stop(DefaultProgressOutput::PROGRESS_NAME)
            ->willReturn($stopwatchEvent->reveal())
            ->shouldBeCalled($stopwatch);

        $violationCount = 5;
        $fileCount = 10;

        $defaultProgressOutput = new DefaultProgressOutput(
            $output->reveal(),
            $stopwatch->reveal()
        );
        $defaultProgressOutput->endProgress($fileCount, $violationCount);
    }
}
