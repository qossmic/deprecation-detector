<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

/**
 * Class ReattachStateToVariable.
 *
 * Reattaches the guessedType of an variable to the AST using the SymbolTable
 *
 * example:
 *
 * $var = new SomeClass();      (Another resolver will set the type of $var to the AST)
 * $var->someMethod();          (The ReattachStateToVariable will set the same guessedType to the $var AST node again)
 */
class ReattachStateToVariable implements ResolverInterface
{
    /**
     * @var SymbolTable
     */
    private $table;

    /**
     * @param SymbolTable $table
     */
    public function __construct(SymbolTable $table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveVariableType(Node $node)
    {
        if ($node instanceof Node\Expr\Variable) {
            $node->setAttribute('guessedType', $this->table->lookUp($node->name)->type());
        }
    }
}
