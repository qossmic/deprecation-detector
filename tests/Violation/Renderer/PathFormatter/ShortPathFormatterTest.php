<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\PathFormatter;

use SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter\ShortPathFormatter;

class ShortPathFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $formatter = new ShortPathFormatter('');

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter\ShortPathFormatter',
            $formatter
        );
    }

    public function testFormatterReturnsStringIfIndexInNotInPath()
    {
        $formatter = new ShortPathFormatter('another/path/prefix');

        $this->assertEquals(
            'path/to/file',
            $formatter->format('path/to/file')
        );
    }

    public function testFormatterRemovesIndexFromPath()
    {
        $formatter = new ShortPathFormatter('path/to/');

        $this->assertEquals(
            'file',
            $formatter->format('path/to/file')
        );
    }
}
