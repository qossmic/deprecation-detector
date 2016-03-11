<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node\Expr\Variable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToVariable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

class ReattachStateToVariableTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize(SymbolTable::class);
        $resolver = new ReattachStateToVariable($table->reveal());

        $this->assertInstanceOf(
            ReattachStateToVariable::class,
            $resolver
        );
    }

    public function testGuessedTypeIsReattached()
    {
        $table = $this->prophesize(SymbolTable::class);
        $table->lookUp('var')->willReturn(new Symbol('var', 'SomeClass'));
        $resolver = new ReattachStateToVariable($table->reveal());

        $node = new Variable('var');

        $resolver->resolveVariableType($node);
        $this->assertSame('SomeClass', $node->getAttribute('guessedType'));
    }
}
