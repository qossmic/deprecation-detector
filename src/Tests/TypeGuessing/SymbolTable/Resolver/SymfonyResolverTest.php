<?php

namespace Tests\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\SymfonyResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Symbol;

class SymfonyResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $container = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader');
        $resolver = new SymfonyResolver($table->reveal(), $container->reveal());

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\SymfonyResolver',
            $resolver
        );
    }

    public function testSimpleCall()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->lookUp('this')->shouldBeCalled()->willReturn(new Symbol('this', 'TestController'));
        $container = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader');
        $resolver = new SymfonyResolver($table->reveal(), $container->reveal());

        $var = new Node\Expr\Variable('this');

        $node1 = new Node\Expr\MethodCall($var, 'getDoctrine');
        $node2 = new Node\Expr\MethodCall($var, 'createForm');
        $node3 = new Node\Expr\MethodCall($var, 'createFormBuilder');

        $resolver->resolveVariableType($node1);
        $resolver->resolveVariableType($node2);
        $resolver->resolveVariableType($node3);

        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Registry', $node1->getAttribute('guessedType'));
        $this->assertEquals('Symfony\Component\Form\Form', $node2->getAttribute('guessedType'));
        $this->assertEquals('Symfony\Component\Form\FormBuilder', $node3->getAttribute('guessedType'));
    }

    public function testSimpleContainerCall()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->lookUp('this')->shouldBeCalled()->willReturn(new Symbol('this', 'TestController'));
        $container = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader');
        $container->has('logger')->shouldBeCalled()->willReturn(true);
        $container->get('logger')->shouldBeCalled()->willReturn('LoggerClass');
        $resolver = new SymfonyResolver($table->reveal(), $container->reveal());

        $var1 = new Node\Expr\Variable('this');
        $var2 = new Node\Expr\Variable('container');

        $args = array(new Node\Arg(new Node\Scalar\String_('logger')));

        $node1 = new Node\Expr\MethodCall($var1, 'get', $args);
        $node2 = new Node\Expr\MethodCall($var2, 'get', $args);

        $resolver->resolveVariableType($node1);
        $resolver->resolveVariableType($node2);

        $this->assertEquals('LoggerClass', $node1->getAttribute('guessedType'));
        $this->assertEquals('LoggerClass', $node2->getAttribute('guessedType'));
    }

    public function testAssignmentCall()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->lookUp('this')->shouldBeCalled()->willReturn(new Symbol('this', 'TestController'));
        $table->setSymbol('test', 'Doctrine\Bundle\DoctrineBundle\Registry')->shouldBeCalled();
        $table->setSymbol('test', 'Symfony\Component\Form\Form')->shouldBeCalled();
        $table->setSymbol('test', 'Symfony\Component\Form\FormBuilder')->shouldBeCalled();
        $container = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader');
        $resolver = new SymfonyResolver($table->reveal(), $container->reveal());

        $var = new Node\Expr\Variable('test');
        $that = new Node\Expr\Variable('this');

        $node1 = new Node\Expr\MethodCall($that, 'getDoctrine');
        $node2 = new Node\Expr\MethodCall($that, 'createForm');
        $node3 = new Node\Expr\MethodCall($that, 'createFormBuilder');

        $assign1 = new Node\Expr\Assign($var, $node1);
        $assign2 = new Node\Expr\Assign($var, $node2);
        $assign3 = new Node\Expr\Assign($var, $node3);

        $resolver->resolveVariableType($assign1);
        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Registry', $assign1->var->getAttribute('guessedType'));
        $resolver->resolveVariableType($assign2);
        $this->assertEquals('Symfony\Component\Form\Form', $assign2->var->getAttribute('guessedType'));
        $resolver->resolveVariableType($assign3);
        $this->assertEquals('Symfony\Component\Form\FormBuilder', $assign3->var->getAttribute('guessedType'));
    }

    public function testAssignmentContainerCall()
    {
        $table = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $table->lookUp('this')->shouldBeCalled()->willReturn(new Symbol('this', 'TestController'));
        $table->setSymbol('test', 'LoggerClass')->shouldBeCalled();
        $table->setSymbol('test', 'LoggerClass2')->shouldBeCalled();
        $container = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader');
        $container->has('logger')->shouldBeCalled()->willReturn(true);
        $container->has('logger2')->shouldBeCalled()->willReturn(true);
        $container->get('logger')->shouldBeCalled()->willReturn('LoggerClass');
        $container->get('logger2')->shouldBeCalled()->willReturn('LoggerClass2');
        $resolver = new SymfonyResolver($table->reveal(), $container->reveal());

        $var = new Node\Expr\Variable('test');

        $that1 = new Node\Expr\Variable('this');
        $that2 = new Node\Expr\Variable('container');

        $args1 = array(new Node\Arg(new Node\Scalar\String_('logger')));
        $args2 = array(new Node\Arg(new Node\Scalar\String_('logger2')));

        $node1 = new Node\Expr\MethodCall($that1, 'get', $args1);
        $node2 = new Node\Expr\MethodCall($that2, 'get', $args2);

        $assign1 = new Node\Expr\Assign($var, $node1);
        $assign2 = new Node\Expr\Assign($var, $node2);

        $resolver->resolveVariableType($assign1);
        $this->assertEquals('LoggerClass', $assign1->var->getAttribute('guessedType'));
        $resolver->resolveVariableType($assign2);
        $this->assertEquals('LoggerClass2', $assign2->var->getAttribute('guessedType'));
    }
}
