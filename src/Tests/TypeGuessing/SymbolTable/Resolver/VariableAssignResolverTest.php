<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\VariableAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;

class VariableAssignResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $resolver = new VariableAssignResolver($table->reveal());

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\VariableAssignResolver',
            $resolver
        );
    }

    public function testAssignPropertyWithClass()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $resolver = new VariableAssignResolver($table->reveal());

        $left = new Variable('var');
        $right = new New_(new Name('SomeClass'));
        $node = new Assign($left, $right);

        $resolver->resolveVariableType($node);
        $this->assertSame('SomeClass', $node->var->getAttribute('guessedType'));
    }

    public function testAssignPropertyWithVariable()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->lookUp('someVar')->willReturn(new Symbol('someVar', 'SomeClass'));
        $table->setSymbol('var', 'SomeClass')->shouldBeCalled();

        $resolver = new VariableAssignResolver($table->reveal());

        $left = new Variable('var');
        $right = new Variable('someVar');
        $node = new Assign($left, $right);

        $resolver->resolveVariableType($node);
        $this->assertSame('SomeClass', $node->var->getAttribute('guessedType'));
    }

    public function testAssignPropertyWithProperty()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->lookUpClassProperty('someProp')->willReturn(new Symbol('someProp', 'SomeClass'));
        $table->setSymbol('var', 'SomeClass')->shouldBeCalled();

        $resolver = new VariableAssignResolver($table->reveal());

        $left = new Variable('var');
        $right = new PropertyFetch(new Variable('this'), 'someProp');
        $node = new Assign($left, $right);

        $resolver->resolveVariableType($node);

        $this->assertSame('SomeClass', $node->var->getAttribute('guessedType'));
    }
}
