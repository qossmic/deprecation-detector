<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable;

use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\TableScope;

class SymbolTableTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = new SymbolTable();

        $this->assertInstanceOf(SymbolTable::class, $table);
    }

    public function testEnterLeaveAndCurrentScope()
    {
        $table = new SymbolTable();

        $this->assertEquals(TableScope::GLOBAL_SCOPE, $table->currentScope()->scope());

        $classScope = $this->prophesize(TableScope::class);
        $classScope->scope()->willReturn(TableScope::CLASS_LIKE_SCOPE);
        $classScope = $classScope->reveal();
        $table->enterScope($classScope);

        $this->assertSame($classScope, $table->currentScope());

        $table->leaveScope();

        $this->assertEquals(TableScope::GLOBAL_SCOPE, $table->currentScope()->scope());
    }

    public function testAddSymbol()
    {
        $table = new SymbolTable();
        $classScope = $this->prophesize(TableScope::class);
        $classScope->scope()->willReturn(TableScope::GLOBAL_SCOPE);
        $classScope->setSymbol(new Symbol('var', 'class'))->shouldBeCalled();
        $classScope = $classScope->reveal();
        $table->enterScope($classScope);

        $table->setSymbol('var', 'class');
    }

    public function testLookUp()
    {
        $table = new SymbolTable();

        $classScope = $this->prophesize(TableScope::class);
        $classScope->findSymbol('var')->willReturn($result = new Symbol('var', 'class'))->shouldBeCalled();

        $methodScope = $this->prophesize(TableScope::class);
        $methodScope->findSymbol('var')->willReturn(null)->shouldBeCalled();

        $table->enterScope($classScope->reveal());
        $table->enterScope($methodScope->reveal());

        $this->assertSame($result, $table->lookUp('var'));
    }

    public function testLookUpReturnsNullSymbol()
    {
        $table = new SymbolTable();

        $this->assertEquals(new Symbol('var', ''), $table->lookUp('var'));
    }

    public function testLookUpClassPropertyReturnsNullSymbolIfThereIsNoClassScope()
    {
        $table = new SymbolTable();

        $this->assertEquals(new Symbol('property', ''), $table->lookUpClassProperty('property'));
    }

    public function testLookUpClassPropertyReturnsNullSymbolIfThereIsNoClassProperty()
    {
        $table = new SymbolTable();
        $classScope = $this->prophesize(TableScope::class);
        $classScope->scope()->willReturn('CLASS_LIKE_SCOPE');
        $classScope->findSymbol('property')->willReturn(null);

        $table->enterScope($classScope->reveal());
        $this->assertEquals(new Symbol('property', ''), $table->lookUpClassProperty('property'));
    }

    public function testLookUpClassPropertyReturnsSymbol()
    {
        $table = new SymbolTable();
        $symbol = $this->prophesize(Symbol::class);
        $classScope = $this->prophesize(TableScope::class);
        $classScope->scope()->willReturn('CLASS_LIKE_SCOPE');
        $classScope->findSymbol('property')->willReturn($symbol->reveal());

        $table->enterScope($classScope->reveal());
        $this->assertEquals($symbol->reveal(), $table->lookUpClassProperty('property'));
    }

    public function testSetClassProperty()
    {
        $table = new SymbolTable();
        $classScope = $this->prophesize(TableScope::class);
        $classScope->scope()->willReturn('CLASS_LIKE_SCOPE');
        $classScope->setSymbol(new Symbol('property', 'type'))->shouldBeCalled();
        $methodScope = $this->prophesize(TableScope::class);
        $methodScope->scope()->willReturn('CLASS_METHOD_SCOPE');

        $table->enterScope($classScope->reveal());
        $table->enterScope($methodScope->reveal());
        $table->setClassProperty('property', 'type');
    }

    public function testSetClassPropertyThrowsException()
    {
        $this->setExpectedException('Exception', 'Illegal State there is no class scope above');

        $table = new SymbolTable();
        $table->setClassProperty('property', 'type');
    }
}
