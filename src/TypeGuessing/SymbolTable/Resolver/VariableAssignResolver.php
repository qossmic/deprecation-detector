<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

/**
 * Class VariableAssignResolver.
 *
 * Attaches the guessedType of a variable assign to the AST using the SymbolTable
 *
 * possible resolves:
 *
 * $var = new SomeClass();                  (attaches the guessedType "someClass" to the $var AST node)
 * $var = $someVar;                         (attaches guessedType of $someVar to the $var AST node)
 * $var = $class->prop;                     (attaches guessedType of $this->prop to the $var AST node)
 */
class VariableAssignResolver implements ResolverInterface
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
        // $x = ...
        if (!$node instanceof Node\Expr\Assign
            || !$node->var instanceof Node\Expr\Variable) {
            return;
        }

        // skips variable names like ${$node->nodeName}
        if (!is_string($node->var->name)) {
            return;
        }

        // $x = new X();
        if ($node->expr instanceof Node\Expr\New_) {
            if ($node->expr->class instanceof Node\Name) {
                $type = $node->expr->class->toString();
                $this->table->setSymbol($node->var->name, $type);
                $node->var->setAttribute('guessedType', $type);
            }
        }

        // $x = $y;
        if ($node->expr instanceof Node\Expr\Variable) {
            $type = $this->table->lookUp($node->expr->name)->type();
            $node->var->setAttribute('guessedType', $type);
            $this->table->setSymbol($node->var->name, $type);
        }

        // $x = $this->x
        if ($node->expr instanceof Node\Expr\PropertyFetch) {
            $type = $this->table->lookUpClassProperty($node->expr->name)->type();
            $node->var->setAttribute('guessedType', $type);
            $this->table->setSymbol($node->var->name, $type);
        }
    }
}
