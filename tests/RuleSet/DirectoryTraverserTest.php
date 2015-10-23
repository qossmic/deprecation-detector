<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;

class DirectoryTraverserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $deprecationFileFinder = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');

        $directoryTraverser = new DirectoryTraverser($deprecationFileFinder->reveal(), $dispatcher->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser', $directoryTraverser);
    }
}
