<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\Finder\Result;
use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;

class DirectoryTraverserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $deprecationFileFinder = $this->prophesize(ParsedPhpFileFinder::class);

        $directoryTraverser = new DirectoryTraverser($deprecationFileFinder->reveal());

        $this->assertInstanceOf(DirectoryTraverser::class, $directoryTraverser);
    }

    public function testTraverse()
    {
        $aPhpFileInfo = $this->prophesize(PhpFileInfo::class);
        $aPhpFileInfo->hasDeprecations()->willReturn(true);
        $aPhpFileInfo->classDeprecations()->willReturn([]);
        $aPhpFileInfo->methodDeprecations()->willReturn([]);
        $aPhpFileInfo->interfaceDeprecations()->willReturn([]);

        $anotherPhpFileInfo = $this->prophesize(PhpFileInfo::class);
        $anotherPhpFileInfo->hasDeprecations()->willReturn(false);

        $deprecationResult = $this->prophesize(Result::class);
        $deprecationResult->parsedFiles()->willReturn([
            $aPhpFileInfo->reveal(),
            $anotherPhpFileInfo->reveal(),
        ]);

        $deprecationFileFinder = $this->prophesize(ParsedPhpFileFinder::class);
        $deprecationFileFinder->parsePhpFiles('some_dir')->willReturn($deprecationResult->reveal());

        $ruleSet = $this->prophesize(RuleSet::class);
        $ruleSet->merge($aPhpFileInfo->reveal())->shouldBeCalled();
        $ruleSet->merge($anotherPhpFileInfo->reveal())->shouldNotBeCalled();

        $directoryTraverser = new DirectoryTraverser($deprecationFileFinder->reveal());
        $directoryTraverser->traverse('some_dir', $ruleSet->reveal());
    }
}
