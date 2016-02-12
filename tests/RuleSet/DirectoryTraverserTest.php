<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;

class DirectoryTraverserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $deprecationFileFinder = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');

        $directoryTraverser = new DirectoryTraverser($deprecationFileFinder->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser', $directoryTraverser);
    }

    public function testTraverse()
    {
        $aPhpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $aPhpFileInfo->hasDeprecations()->willReturn(true);
        $aPhpFileInfo->classDeprecations()->willReturn(array());
        $aPhpFileInfo->methodDeprecations()->willReturn(array());
        $aPhpFileInfo->interfaceDeprecations()->willReturn(array());

        $anotherPhpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $anotherPhpFileInfo->hasDeprecations()->willReturn(false);

        $deprecationResult = $this->prophesize('SensioLabs\DeprecationDetector\Finder\Result');
        $deprecationResult->parsedFiles()->willReturn(array(
            $aPhpFileInfo->reveal(),
            $anotherPhpFileInfo->reveal(),
        ));

        $deprecationFileFinder = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $deprecationFileFinder->parsePhpFiles('some_dir')->willReturn($deprecationResult->reveal());

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $ruleSet->merge($aPhpFileInfo->reveal())->shouldBeCalled();
        $ruleSet->merge($anotherPhpFileInfo->reveal())->shouldNotBeCalled();

        $directoryTraverser = new DirectoryTraverser($deprecationFileFinder->reveal());
        $directoryTraverser->traverse('some_dir', $ruleSet->reveal());
    }
}
