<?php

namespace SensioLabs\DeprecationDetector\Tests\Console\Output;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\Console\Output\VerboseProgressOutput;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use Symfony\Component\Console\Helper\ProgressBar;

class VerboseProgressOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $progressBar = $this->prophesize(ProgressBar::class);

        $verboseProgressOutput = new VerboseProgressOutput(
            $progressBar->reveal(),
            false,
            'a progress bar label'
        );

        $this->assertInstanceOf(
            VerboseProgressOutput::class,
            $verboseProgressOutput
        );
    }

    public function testStart()
    {
        $progressBar = $this->prophesize(ProgressBar::class);
        $progressBar->setFormat(Argument::any())->shouldBeCalled();
        $progressBar->start(Argument::any())->shouldBeCalled();

        $verboseProgressOutput = new VerboseProgressOutput(
            $progressBar->reveal(),
            true,
            'a progress bar label'
        );

        $verboseProgressOutput->start(10);
    }

    public function testStartGeneratedOutputOnlyInVerboseMode()
    {
        $progressBar = $this->prophesize(ProgressBar::class);
        $progressBar->setFormat(Argument::any())->shouldNotBeCalled();
        $progressBar->start(Argument::any())->shouldNotBeCalled();

        $verboseProgressOutput = new VerboseProgressOutput(
            $progressBar->reveal(),
            false,
            'a progress bar label'
        );

        $verboseProgressOutput->start(10);
    }

    public function testAdvanceSetsFormatIfIsFirstFileProcessed()
    {
        $label = 'a progress bar label';
        $format = '<info>%message%</info>'."\n".$label.': <info>%current%</info>/<info>%max%</info>';

        $progressBar = $this->prophesize(ProgressBar::class);
        $progressBar->clear()->shouldBeCalled();
        $progressBar->advance()->shouldBeCalled();
        $progressBar->display()->shouldBeCalled();
        $progressBar->setFormat($format)->shouldBeCalled();

        $phpFileInfo = $this->prophesize(PhpFileInfo::class);
        $phpFileInfo->getRelativePathname()->willReturn($message = 'Path/To/The/Parsed/File');
        $progressBar->setMessage($message)->shouldBeCalled();

        $verboseProgressOutput = new VerboseProgressOutput(
            $progressBar->reveal(),
            true,
            $label
        );

        $verboseProgressOutput->advance(1, $phpFileInfo->reveal());
    }

    public function testAdvanceGeneratedOutputOnlyInVerboseMode()
    {
        $progressBar = $this->prophesize(ProgressBar::class);
        $progressBar->clear()->shouldNotBeCalled();
        $progressBar->setFormat(Argument::any())->shouldNotBeCalled();
        $progressBar->setMessage(Argument::any())->shouldNotBeCalled();
        $progressBar->clear()->shouldNotBeCalled();
        $progressBar->advance()->shouldNotBeCalled();
        $progressBar->display()->shouldNotBeCalled();

        $phpFileInfo = $this->prophesize(PhpFileInfo::class);

        $verboseProgressOutput = new VerboseProgressOutput(
            $progressBar->reveal(),
            false,
            'a progress bar label'
        );

        $verboseProgressOutput->advance(100, $phpFileInfo->reveal());
    }

    public function testEnd()
    {
        $progressBar = $this->prophesize(ProgressBar::class);
        $progressBar->clear()->shouldBeCalled();
        $progressBar->finish()->shouldBeCalled();

        $verboseProgressOutput = new VerboseProgressOutput(
            $progressBar->reveal(),
            true,
            'a progress bar label'
        );

        $verboseProgressOutput->end();
    }

    public function testEndGeneratedOutputOnlyInVerboseMode()
    {
        $progressBar = $this->prophesize(ProgressBar::class);
        $progressBar->clear()->shouldNotBeCalled();
        $progressBar->finish()->shouldNotBeCalled();

        $verboseProgressOutput = new VerboseProgressOutput(
            $progressBar->reveal(),
            false,
            'a progress bar label'
        );

        $verboseProgressOutput->end();
    }
}
