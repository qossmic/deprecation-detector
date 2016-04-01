<?php

namespace SensioLabs\DeprecationDetector\Tests\Parser;

use PhpParser\NodeTraverser;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Parser\UsageParser;
use SensioLabs\DeprecationDetector\Visitor\StaticAnalysisVisitorInterface;
use SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface;

class UsageParserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $violationVisitor = $this
            ->prophesize(ViolationVisitorInterface::class)
            ->reveal();
        $staticAnalysisVisitor = $this
            ->prophesize(StaticAnalysisVisitorInterface::class)
            ->reveal();

        $baseTraverser = $this->prophesize(NodeTraverser::class);

        $staticAnalysisTraverser = $this->prophesize(NodeTraverser::class);
        $staticAnalysisTraverser->addVisitor($staticAnalysisVisitor)->shouldBeCalled();

        $violationTraverser = $this->prophesize(NodeTraverser::class);
        $violationTraverser->addVisitor($violationVisitor)->shouldBeCalled();

        $deprecationParser = new UsageParser(
            [$staticAnalysisVisitor],
            [$violationVisitor],
            $baseTraverser->reveal(),
            $staticAnalysisTraverser->reveal(),
            $violationTraverser->reveal()
        );

        $this->assertInstanceOf(UsageParser::class, $deprecationParser);
    }

    public function testParseFile()
    {
        $phpFileInfo = $this->prophesize(PhpFileInfo::class);
        $phpFileInfo->getContents()->willReturn('');
        $phpFileInfo = $phpFileInfo->reveal();

        $violationVisitor = $this
            ->prophesize(ViolationVisitorInterface::class)
            ->reveal();

        $baseTraverser = $this->prophesize(NodeTraverser::class);
        $baseTraverser->traverse([])->willReturn([])->shouldBeCalled();
        $staticAnalysisTraverser = $this->prophesize(NodeTraverser::class);
        $staticAnalysisTraverser->traverse([])->willReturn([])->shouldBeCalled();

        $violationTraverser = $this->prophesize(NodeTraverser::class);
        $violationTraverser->addVisitor($violationVisitor)->shouldBeCalled();
        $violationTraverser->traverse([])->shouldBeCalled();

        $deprecationParser = new UsageParser(
            [],
            [$violationVisitor],
            $baseTraverser->reveal(),
            $staticAnalysisTraverser->reveal(),
            $violationTraverser->reveal()
        );

        $deprecationParser->parseFile($phpFileInfo);
    }
}
