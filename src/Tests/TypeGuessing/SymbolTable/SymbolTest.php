<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable;

use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;

class SymbolTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $symbol = new Symbol('var', 'class');

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol', $symbol);
    }

    public function testGetSymbol()
    {
        $symbol = new Symbol('var', 'class');

        $this->assertSame('var', $symbol->symbol());
    }

    public function testGetType()
    {
        $symbol = new Symbol('var', 'class');

        $this->assertSame('class', $symbol->type());
    }
}
