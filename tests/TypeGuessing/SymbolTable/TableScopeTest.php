<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable;

use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\TableScope;

class TableScopeTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $scope = new TableScope(TableScope::CLASS_LIKE_SCOPE);

        $this->assertInstanceOf(TableScope::class, $scope);
    }

    public function testScope()
    {
        $scope = new TableScope(TableScope::CLASS_LIKE_SCOPE);

        $this->assertSame(TableScope::CLASS_LIKE_SCOPE, $scope->scope());
    }

    public function testFindSymbolReturnsNull()
    {
        $scope = new TableScope(TableScope::CLASS_LIKE_SCOPE);

        $this->assertNull($scope->findSymbol('var'));
    }

    public function testSetAndFindSymbol()
    {
        $scope = new TableScope(TableScope::CLASS_LIKE_SCOPE);
        $scope->setSymbol($result = new Symbol('var', 'class'));

        $this->assertSame($result, $scope->findSymbol('var'));
    }
}
