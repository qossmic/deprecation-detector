<?php

namespace SensioLabs\DeprecationDetector\Tests\Parser;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\Parser\DeprecationParser;

class DeprecationParserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $deprecationParser = new DeprecationParser([], $this->prophesize('PhpParser\NodeTraverser')->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Parser\DeprecationParser', $deprecationParser);
    }

    public function testAddDeprecationVisitor()
    {
        $visitor = $this->prophesize('SensioLabs\DeprecationDetector\Visitor\DeprecationVisitorInterface');
        $visitor = $visitor->reveal();

        $baseTraverser = $this->prophesize('PhpParser\NodeTraverser');
        $baseTraverser->addVisitor($visitor)->shouldBeCalled();

        $deprecationParser = new DeprecationParser([], $baseTraverser->reveal());
        $deprecationParser->addDeprecationVisitor($visitor);
    }

    public function testParseFile()
    {
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo')->reveal();

        $visitor = $this->prophesize('SensioLabs\DeprecationDetector\Visitor\DeprecationVisitorInterface');
        $visitor->setPhpFileInfo($phpFileInfo)->shouldBeCalled();
        $anotherVisitor = $this->prophesize('SensioLabs\DeprecationDetector\Visitor\DeprecationVisitorInterface');
        $anotherVisitor->setPhpFileInfo($phpFileInfo)->shouldBeCalled();

        $baseTraverser = $this->prophesize('PhpParser\NodeTraverser');
        $baseTraverser->addVisitor($visitor)->shouldBeCalled();
        $baseTraverser->addVisitor($anotherVisitor)->shouldBeCalled();
        $baseTraverser->traverse(Argument::any())->shouldBeCalled();

        $deprecationParser = new DeprecationParser(
            array($visitor->reveal(), $anotherVisitor->reveal()),
            $baseTraverser->reveal()
        );
        $deprecationParser->parseFile($phpFileInfo);
    }
}
