<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Console\Output\VerboseProgressOutput;
use SensioLabs\DeprecationDetector\Finder\FinderFactoryInterface;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\Parser\ParserInterface;

class ParsedPhpFileFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $parser = $this->prophesize(ParserInterface::class);
        $progressOutput = $this->prophesize(
            VerboseProgressOutput::class
        );
        $finderFactory = $this->prophesize(FinderFactoryInterface::class);

        $finder = new ParsedPhpFileFinder(
            $parser->reveal(),
            $progressOutput->reveal(),
            $finderFactory->reveal()
        );

        $this->assertInstanceOf(ParsedPhpFileFinder::class, $finder);
    }
}
