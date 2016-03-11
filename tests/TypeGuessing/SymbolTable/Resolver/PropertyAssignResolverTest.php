<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\PropertyAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

class PropertyAssignResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize(SymbolTable::class);
        $resolver = new PropertyAssignResolver($table->reveal());

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\PropertyAssignResolver',
            $resolver
        );
    }

    public function testAssignPropertyWithClass()
    {
        $table = $this->prophesize(SymbolTable::class);
        $resolver = new PropertyAssignResolver($table->reveal());

        $left = new PropertyFetch(new Variable('this'), 'prop');
        $right = new New_(new Name('SomeClass'));
        $node = new Assign($left, $right);

        $resolver->resolveVariableType($node);
        $this->assertSame('SomeClass', $node->var->getAttribute('guessedType'));
    }

    public function testAssignPropertyWithVariable()
    {
        $table = $this->prophesize(SymbolTable::class);
        $table->lookUp('someVar')->willReturn(new Symbol('someVar', 'SomeClass'));
        $table->setClassProperty('prop', 'SomeClass')->shouldBeCalled();

        $resolver = new PropertyAssignResolver($table->reveal());

        $left = new PropertyFetch(new Variable('this'), 'prop');
        $right = new Variable('someVar');
        $node = new Assign($left, $right);

        $resolver->resolveVariableType($node);
        $this->assertSame('SomeClass', $node->var->getAttribute('guessedType'));
    }

    public function testAssignPropertyVariableWithVariable()
    {
        $table = $this->prophesize(SymbolTable::class);
        $table->setClassProperty('prop', 'SomeClass')->shouldNotBeCalled();

        $resolver = new PropertyAssignResolver($table->reveal());

        $left = new PropertyFetch(new Variable('this'), new Variable('prop'));
        $right = new Variable('someVar');
        $node = new Assign($left, $right);

        $resolver->resolveVariableType($node);
        $this->assertNull($node->var->getAttribute('guessedType'));
    }

    public function testAssignPropertyWithProperty()
    {
        $table = $this->prophesize(SymbolTable::class);
        $table->lookUpClassProperty('someProp')->willReturn(new Symbol('someProp', 'SomeClass'));
        $table->setClassProperty('prop', 'SomeClass')->shouldBeCalled();

        $resolver = new PropertyAssignResolver($table->reveal());

        $left = new PropertyFetch(new Variable('this'), 'prop');
        $right = new PropertyFetch(new Variable('this'), 'someProp');
        $node = new Assign($left, $right);

        $resolver->resolveVariableType($node);

        $this->assertSame('SomeClass', $node->var->getAttribute('guessedType'));
    }
}
