<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $result = new Result(array(), array(), 10);

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Finder\Result', $result);
    }

    public function testParsedFiles()
    {
        $parsedFiles = array(
            $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo'),
        );

        $result = new Result($parsedFiles, array(), 10);

        $this->assertSame($parsedFiles, $result->parsedFiles());
    }

    public function testParserErrors()
    {
        $parserErrors = array(
            $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo'),
        );

        $result = new Result(array(), $parserErrors, 10);

        $this->assertSame($parserErrors, $result->parserErrors());
    }

    public function testFileCount()
    {
        $result = new Result(array(), array(), 10);

        $this->assertSame(10, $result->fileCount());
    }
}
