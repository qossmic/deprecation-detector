<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node\Name;
use PhpParser\Node\Param;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ArgumentResolver;

class ArgumentResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $resolver = new ArgumentResolver($table->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ArgumentResolver', $resolver);
    }

    public function testResolveTypedArgument()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->setSymbol('var', 'SomeClass')->shouldBeCalled();
        $resolver = new ArgumentResolver($table->reveal());

        $param = new Param('var', null, new Name('SomeClass'));
        $resolver->resolveVariableType($param);
    }

    public function testSkipsUntypedArgument()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->setSymbol('var', 'SomeClass')->shouldNotBeCalled();
        $resolver = new ArgumentResolver($table->reveal());

        $param = new Param('var');
        $resolver->resolveVariableType($param);
    }
}
