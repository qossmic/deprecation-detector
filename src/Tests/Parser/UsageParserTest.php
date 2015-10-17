<?php

namespace SensioLabs\DeprecationDetector\Tests\Parser;

use SensioLabs\DeprecationDetector\Parser\UsageParser;

class UsageParserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $violationVisitor = $this->prophesize('SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface')->reveal();
        $staticAnalysisVisitor = $this->prophesize('SensioLabs\DeprecationDetector\Visitor\StaticAnalysisVisitorInterface')->reveal();

        $baseTraverser = $this->prophesize('PhpParser\NodeTraverser');

        $staticAnalysisTraverser = $this->prophesize('PhpParser\NodeTraverser');
        $staticAnalysisTraverser->addVisitor($staticAnalysisVisitor)->shouldBeCalled();

        $violationTraverser = $this->prophesize('PhpParser\NodeTraverser');
        $violationTraverser->addVisitor($violationVisitor)->shouldBeCalled();

        $deprecationParser = new UsageParser([$staticAnalysisVisitor], [$violationVisitor],
            $baseTraverser->reveal(),
            $staticAnalysisTraverser->reveal(),
            $violationTraverser->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Parser\UsageParser', $deprecationParser);
    }

    public function testParseFile()
    {
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $phpFileInfo->getContents()->willReturn('');
        $phpFileInfo = $phpFileInfo->reveal();

        $violationVisitor = $this->prophesize('SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface')->reveal();

        $baseTraverser = $this->prophesize('PhpParser\NodeTraverser');
        $baseTraverser->traverse([])->willReturn([])->shouldBeCalled();
        $staticAnalysisTraverser = $this->prophesize('PhpParser\NodeTraverser');
        $staticAnalysisTraverser->traverse([])->willReturn([])->shouldBeCalled();

        $violationTraverser = $this->prophesize('PhpParser\NodeTraverser');
        $violationTraverser->addVisitor($violationVisitor)->shouldBeCalled();
        $violationTraverser->traverse([])->shouldBeCalled();

        $deprecationParser = new UsageParser([], [$violationVisitor],
            $baseTraverser->reveal(),
            $staticAnalysisTraverser->reveal(),
            $violationTraverser->reveal()
        );

        $deprecationParser->parseFile($phpFileInfo);
    }
}
