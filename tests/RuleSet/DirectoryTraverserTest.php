<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;

class DirectoryTraverserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $deprecationParser = $this->prophesize('SensioLabs\DeprecationDetector\Parser\DeprecationParser');
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');

        $directoryTraverser = new DirectoryTraverser($deprecationParser->reveal(), $dispatcher->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser', $directoryTraverser);
    }
}
