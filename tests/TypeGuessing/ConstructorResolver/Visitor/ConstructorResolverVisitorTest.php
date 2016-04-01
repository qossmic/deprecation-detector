<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\ConstructorResolver\Visitor;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\ConstructorResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\Visitor\ConstructorResolverVisitor;

class ConstructorResolverVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $resolver = $this
            ->prophesize(ConstructorResolver::class);
        $visitor = new ConstructorResolverVisitor($resolver->reveal());

        $this->assertInstanceOf(
            ConstructorResolverVisitor::class,
            $visitor
        );
    }

    public function testComputesClassNodes()
    {
        $node = new Class_('SomeClass');
        $resolver = $this
            ->prophesize(ConstructorResolver::class);
        $resolver->resolveConstructor($node)->shouldBeCalled();
        $visitor = new ConstructorResolverVisitor($resolver->reveal());

        $visitor->enterNode($node);
    }

    public function testDoesNotComputeOtherNodes()
    {
        $node = new Function_('someFunction');
        $resolver = $this
            ->prophesize(ConstructorResolver::class);
        $resolver->resolveConstructor($node)->shouldNotBeCalled();
        $visitor = new ConstructorResolverVisitor($resolver->reveal());

        $visitor->enterNode($node);
    }
}
