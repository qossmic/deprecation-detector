<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToProperty;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

class ReattachStateToPropertyTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize(SymbolTable::class);
        $resolver = new ReattachStateToProperty($table->reveal());

        $this->assertInstanceOf(
            ReattachStateToProperty::class,
            $resolver
        );
    }

    public function testGuessedTypeIsReattached()
    {
        $table = $this->prophesize(SymbolTable::class);
        $table->lookUpClassProperty('prop')->willReturn(new Symbol('prop', 'SomeClass'));

        $resolver = new ReattachStateToProperty($table->reveal());

        $node = new PropertyFetch(new Variable('this'), 'prop');

        $resolver->resolveVariableType($node);
        $this->assertSame('SomeClass', $node->getAttribute('guessedType'));
    }
}
