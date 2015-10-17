<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\ComposedResolver;

class ComposedResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $resolver = new ComposedResolver();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\ComposedResolver', $resolver);
    }

    public function testCallsEveryResolverOnce()
    {
        $composedResolver = new ComposedResolver();

        $someResolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ResolverInterface');
        $someResolver->resolveVariableType(Argument::any())->shouldBeCalled();

        $someOtherResolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ResolverInterface');
        $someOtherResolver->resolveVariableType(Argument::any())->shouldBeCalled();

        $node = $this->prophesize('PhpParser\Node');

        $composedResolver->addResolver($someResolver->reveal());
        $composedResolver->addResolver($someOtherResolver->reveal());
        $composedResolver->resolveVariableType($node->reveal());
    }
}
