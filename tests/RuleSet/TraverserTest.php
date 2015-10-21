<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\RuleSet\Traverser;

class TraverserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $container = $this->prophesize('Pimple\Container');
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');

        $traverser = new Traverser($container->reveal(), $dispatcher->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Traverser', $traverser);
    }
}
