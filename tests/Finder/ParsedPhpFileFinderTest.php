<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;

class ParsedPhpFileFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $parser = $this->prophesize('SensioLabs\DeprecationDetector\Parser\ParserInterface');
        $progressOutput = $this->prophesize(
            'SensioLabs\DeprecationDetector\Console\Output\VerboseProgressOutput'
        );
        $finderFactory = $this->prophesize('SensioLabs\DeprecationDetector\Finder\FinderFactoryInterface');

        $finder = new ParsedPhpFileFinder(
            $parser->reveal(),
            $progressOutput->reveal(),
            $finderFactory->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder', $finder);
    }
}
