<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Finder\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $result = new Result(array(), array(), 10);

        $this->assertInstanceOf(Result::class, $result);
    }

    public function testParsedFiles()
    {
        $parsedFiles = array(
            $this->prophesize(PhpFileInfo::class),
        );

        $result = new Result($parsedFiles, array(), 10);

        $this->assertSame($parsedFiles, $result->parsedFiles());
    }

    public function testParserErrors()
    {
        $parserErrors = array(
            $this->prophesize(PhpFileInfo::class),
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
