<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node;

interface ResolverInterface
{
    /**
     * @param Node $node
     */
    public function resolveVariableType(Node $node);
}
