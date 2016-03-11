<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\ConstructorResolver;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Prophecy\Argument;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\ConstructorResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\TableScope;
use SensioLabs\DeprecationDetector\Visitor\VisitorInterface;

class ConstructorResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize(SymbolTable::class);
        $resolver = new ConstructorResolver($table->reveal());

        $this->assertInstanceOf(
            ConstructorResolver::class,
            $resolver
        );
    }

    public function testSkipsAbstractConstructor()
    {
        $classMethods = array(
            new ClassMethod(
                '__construct',
                array(
                    'type' => Class_::MODIFIER_ABSTRACT,
                )
            ),
        );
        $classNode = new Class_('SomeClass');
        $classNode->stmts = $classMethods;

        $table = $this->prophesize(SymbolTable::class);
        $table->enterScope(new TableScope(TableScope::CLASS_METHOD_SCOPE))->shouldNotBeCalled();
        $visitor = $this->prophesize(VisitorInterface::class);
        $visitor->beforeTraverse(Argument::any())->shouldNotBeCalled();
        $visitor->enterNode(Argument::any())->shouldNotBeCalled();
        $visitor->afterTraverse(Argument::any())->shouldNotBeCalled();
        $visitor->leaveNode(Argument::any())->shouldNotBeCalled();

        $resolver = new ConstructorResolver($table->reveal());
        $resolver->addVisitor($visitor->reveal());

        $resolver->resolveConstructor($classNode);
    }

    public function testResolveConstructorAndAddVisitors()
    {
        $classMethod = new ClassMethod('__construct');
        $classMethod->stmts = array($node = new Variable('x'));
        $classMethods = array($classMethod);
        $classNode = new Class_('SomeClass');
        $classNode->stmts = $classMethods;

        $table = $this->prophesize(SymbolTable::class);
        $table->enterScope(new TableScope(TableScope::CLASS_METHOD_SCOPE))->shouldBeCalled();
        $table->leaveScope()->shouldBeCalled();
        $visitor = $this->prophesize(VisitorInterface::class);
        $visitor->beforeTraverse(Argument::any())->shouldBeCalled();
        $visitor->enterNode(Argument::any())->shouldBeCalled();
        $visitor->afterTraverse(Argument::any())->shouldBeCalled();
        $visitor->leaveNode(Argument::any())->shouldBeCalled();

        $resolver = new ConstructorResolver($table->reveal());
        $resolver->addVisitor($visitor->reveal());

        $resolver->resolveConstructor($classNode);
    }

    public function testDoesNotResolveOtherClassMethods()
    {
        $classMethod = new ClassMethod('someMethod');
        $classNode = new Class_('SomeClass');
        $classNode->stmts = array($classMethod);

        $table = $this->prophesize(SymbolTable::class);
        $table->enterScope(new TableScope(TableScope::CLASS_METHOD_SCOPE))->shouldNotBeCalled();
        $table->leaveScope()->shouldNotBeCalled();
        $visitor = $this->prophesize(VisitorInterface::class);
        $visitor->beforeTraverse(Argument::any())->shouldNotBeCalled();
        $visitor->enterNode(Argument::any())->shouldNotBeCalled();
        $visitor->afterTraverse(Argument::any())->shouldNotBeCalled();
        $visitor->leaveNode(Argument::any())->shouldNotBeCalled();

        $resolver = new ConstructorResolver($table->reveal());
        $resolver->addVisitor($visitor->reveal());

        $this->assertSame($classNode, $resolver->resolveConstructor($classNode));
    }
}
