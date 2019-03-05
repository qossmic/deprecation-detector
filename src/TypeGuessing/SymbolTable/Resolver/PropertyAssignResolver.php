<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;

/**
 * Class PropertyAssignResolver.
 *
 * Attaches the guessedType of a property assign to the AST using the SymbolTable
 *
 * possible resolves:
 *
 * $this->prop = new SomeClass();               (attaches the guessedType "SomeClass" to the $this->prop AST node)
 * $this->prop = $var;                          (attaches guessedType of $var to the $this->prop AST node)
 * $this->prop = $this->anotherProp;            (attaches guessedType of $this->prop to the $this->prop AST node)
 *
 * @TODO: implement PropertyAssignResolver more generic example: $someClass->prop = new SomeClass();
 */
class PropertyAssignResolver implements ResolverInterface
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
        if ($node instanceof Node\Expr\Assign) {
            // $this->x = ...
            // excluding $this->$x = ...
            if ($node->var instanceof Node\Expr\PropertyFetch) {
                // $stub[$key]->x = ... ; if not tested a php notice will occur
                // @TODO change to be able to use all types of properties like $x->x = 10
                if ($node->var->var instanceof Node\Expr\ArrayDimFetch) {
                    return;
                }

                // @TODO change to be able to use all types of properties like $x->x = 10
                if ($node->var->var->name !== 'this' ||
                    $node->var->name instanceof Node\Expr\Variable ||
                    $node->var->name instanceof Node\Identifier && !is_string($node->var->name->toString())
                ) {
                    return;
                }

                // $this->x = new X();
                if ($node->expr instanceof Node\Expr\New_) {
                    if ($node->expr->class instanceof Node\Name) {
                        $type = $node->expr->class->toString();
                        $node->var->setAttribute('guessedType', $type);
                        $this->table->setClassProperty($node->var->name->toString(), $type);
                    }
                }

                // $this->x = $y;
                if ($node->expr instanceof Node\Expr\Variable) {
                    $type = $this->table->lookUp($node->expr->name)->type();
                    $node->var->setAttribute('guessedType', $type);
                    $this->table->setClassProperty($node->var->name->toString(), $type);
                }

                // $this->x = $this->y;
                if ($node->expr instanceof Node\Expr\PropertyFetch) {
                    $type = $this->table->lookUpClassProperty($node->expr->name)->type();
                    $node->var->setAttribute('guessedType', $type);
                    $this->table->setClassProperty($node->var->name->toString(), $type);
                }
            }
        }
    }
}
