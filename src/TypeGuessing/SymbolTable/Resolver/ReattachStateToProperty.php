<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

/**
 * Class ReattachStateToProperty.
 *
 * Reattaches the guessedType of an class property to the AST using the SymbolTable
 *
 * example:
 *
 * $this->prop = new SomeClass();      (Another resolver will set the type of $this->prop to the AST)
 * $this->prop->someMethod();          (The ReattachStateToProperty will set the same guessedType to the $this->prop AST node again)
 */
class ReattachStateToProperty implements ResolverInterface
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
        if ($node instanceof Node\Expr\PropertyFetch) {
            // $this->someProperty
            if ($node->var instanceof Node\Expr\Variable && $node->var->name === 'this') {
                $node->setAttribute('guessedType', $this->table->lookUpClassProperty($node->name->toString())->type());
            }

            // $x->someProperty
        }
    }
}
