<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToProperty;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;

class ReattachStateToPropertyTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $resolver = new ReattachStateToProperty($table->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToProperty', $resolver);
    }

    public function testGuessedTypeIsReattached()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->lookUpClassProperty('prop')->willReturn(new Symbol('prop', 'SomeClass'));

        $resolver = new ReattachStateToProperty($table->reveal());

        $node = new PropertyFetch(new Variable('this'), 'prop');

        $resolver->resolveVariableType($node);
        $this->assertSame('SomeClass', $node->getAttribute('guessedType'));
    }
}
