<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\ConstructorResolver;
use SensioLabs\DeprecationDetector\Visitor\StaticAnalysisVisitorInterface;

class ConstructorResolverVisitor extends NodeVisitorAbstract implements StaticAnalysisVisitorInterface
{
    private $constructorResolver;

    public function __construct(ConstructorResolver $constructorResolver)
    {
        $this->constructorResolver = $constructorResolver;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            return $this->constructorResolver->resolveConstructor($node);
        }
    }
}
