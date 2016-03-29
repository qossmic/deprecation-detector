<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable;

use PhpParser\Node;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ResolverInterface;

class ComposedResolver implements ResolverInterface
{
    /**
     * @var ResolverInterface[]
     */
    private $resolver;

    public function __construct()
    {
        $this->resolver = [];
    }

    /**
     * @param ResolverInterface $resolver
     */
    public function addResolver(ResolverInterface $resolver)
    {
        $this->resolver[] = $resolver;
    }

    public function resolveVariableType(Node $node)
    {
        foreach ($this->resolver as $resolver) {
            $resolver->resolveVariableType($node);
        }
    }
}
