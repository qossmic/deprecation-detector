<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node\Name;
use PhpParser\Node\Param;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ArgumentResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

class ArgumentResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize(SymbolTable::class);
        $resolver = new ArgumentResolver($table->reveal());

        $this->assertInstanceOf(
            ArgumentResolver::class,
            $resolver
        );
    }

    public function testResolveTypedArgument()
    {
        $table = $this->prophesize(SymbolTable::class);
        $table->setSymbol('var', 'SomeClass')->shouldBeCalled();
        $resolver = new ArgumentResolver($table->reveal());

        $param = new Param('var', null, new Name('SomeClass'));
        $resolver->resolveVariableType($param);
    }

    public function testSkipsUntypedArgument()
    {
        $table = $this->prophesize(SymbolTable::class);
        $table->setSymbol('var', 'SomeClass')->shouldNotBeCalled();
        $resolver = new ArgumentResolver($table->reveal());

        $param = new Param('var');
        $resolver->resolveVariableType($param);
    }
}
