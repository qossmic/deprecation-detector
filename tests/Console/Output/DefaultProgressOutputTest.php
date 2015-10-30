<?php

namespace SensioLabs\DeprecationDetector\Tests\Console\Output;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput;

class DefaultProgressOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $stopwatch = $this->prophesize('Symfony\Component\Stopwatch\Stopwatch');

        $defaultProgressOutput = new DefaultProgressOutput(
            $output->reveal(),
            $stopwatch->reveal()
        );

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput',
            $defaultProgressOutput
        );
    }

    public function testDifferentStages()
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->writeln(Argument::any())->shouldBeCalledTimes(9);
        $stopwatch = $this->prophesize('Symfony\Component\Stopwatch\Stopwatch');
        $stopwatch->start(DefaultProgressOutput::PROGRESS_NAME)->shouldBeCalled();
        $stopwatchEvent = $this->prophesize('Symfony\Component\Stopwatch\StopwatchEvent');
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
        $defaultProgressOutput->endProgress(0,0);
    }

    public function testEndProgressWithoutViolations()
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->writeln(Argument::any())->shouldBeCalledTimes(2);
        $stopwatchEvent = $this->prophesize('Symfony\Component\Stopwatch\StopwatchEvent');
        $stopwatch = $this->prophesize('Symfony\Component\Stopwatch\Stopwatch');
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
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->writeln(Argument::any())->shouldBeCalledTimes(2);
        $stopwatchEvent = $this->prophesize('Symfony\Component\Stopwatch\StopwatchEvent');
        $stopwatch = $this->prophesize('Symfony\Component\Stopwatch\Stopwatch');
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
