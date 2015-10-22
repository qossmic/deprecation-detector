<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;

class ParsedPhpFileFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $parser = $this->prophesize('SensioLabs\DeprecationDetector\Parser\ParserInterface');
        $finder = new ParsedPhpFileFinder($parser->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder', $finder);
    }
}
