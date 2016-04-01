<?php

namespace SensioLabs\DeprecationDetector\Tests\Parser;

use PhpParser\NodeTraverser;
use Prophecy\Argument;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Parser\DeprecationParser;
use SensioLabs\DeprecationDetector\Visitor\DeprecationVisitorInterface;

class DeprecationParserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $deprecationParser = new DeprecationParser([], $this->prophesize(NodeTraverser::class)->reveal());

        $this->assertInstanceOf(DeprecationParser::class, $deprecationParser);
    }

    public function testAddDeprecationVisitor()
    {
        $visitor = $this->prophesize(DeprecationVisitorInterface::class);
        $visitor = $visitor->reveal();

        $baseTraverser = $this->prophesize(NodeTraverser::class);
        $baseTraverser->addVisitor($visitor)->shouldBeCalled();

        $deprecationParser = new DeprecationParser([], $baseTraverser->reveal());
        $deprecationParser->addDeprecationVisitor($visitor);
    }

    public function testParseFile()
    {
        $phpFileInfo = $this->prophesize(PhpFileInfo::class)->reveal();

        $visitor = $this->prophesize(DeprecationVisitorInterface::class);
        $visitor->setPhpFileInfo($phpFileInfo)->shouldBeCalled();
        $anotherVisitor = $this->prophesize(DeprecationVisitorInterface::class);
        $anotherVisitor->setPhpFileInfo($phpFileInfo)->shouldBeCalled();

        $baseTraverser = $this->prophesize(NodeTraverser::class);
        $baseTraverser->addVisitor($visitor)->shouldBeCalled();
        $baseTraverser->addVisitor($anotherVisitor)->shouldBeCalled();
        $baseTraverser->traverse(Argument::any())->shouldBeCalled();

        $deprecationParser = new DeprecationParser(
            [$visitor->reveal(), $anotherVisitor->reveal()],
            $baseTraverser->reveal()
        );
        $deprecationParser->parseFile($phpFileInfo);
    }
}
