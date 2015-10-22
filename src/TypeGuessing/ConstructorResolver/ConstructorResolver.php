<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\TableScope;
use SensioLabs\DeprecationDetector\Visitor\VisitorInterface;

class ConstructorResolver
{
    /**
     * @var SymbolTable
     */
    private $table;

    /**
     * @var VisitorInterface[]
     */
    private $visitors;

    /**
     * @param SymbolTable $table
     * @param VisitorInterface[] $visitors
     */
    public function __construct(SymbolTable $table, array $visitors = array())
    {
        $this->table = $table;
        $this->visitors = $visitors;
    }

    /**
     * @param VisitorInterface $visitor
     */
    public function addVisitor(VisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }

    /**
     * @param Node\Stmt\Class_ $node
     *
     * @return Node\Stmt\Class_
     */
    public function resolveConstructor(Node\Stmt\Class_ $node)
    {
        foreach ($node->stmts as $key => $stmt) {
            if ($stmt instanceof Node\Stmt\ClassMethod && $stmt->name === '__construct') {
                // this will make problems we need an layer above which chains the variable resolvers
                // because we need may need more than this resolver

                // skip constructor if is abstract
                if ($stmt->isAbstract()) {
                    return $node;
                }

                // change recursivly the nodes
                $subTraverser = new NodeTraverser();
                foreach ($this->visitors as $visitor) {
                    $subTraverser->addVisitor($visitor);
                }

                // the table switches to a method scope
                // $x = ... will be treated normal
                // $this->x = ... will be stored in the above class scope and is available afterwards
                $this->table->enterScope(new TableScope(TableScope::CLASS_METHOD_SCOPE));
                $subTraverser->traverse($stmt->params);
                $nodes = $subTraverser->traverse($stmt->stmts);
                $this->table->leaveScope();

                //override the old statement
                $stmt->stmts = $nodes;

                // override the classmethod statement in class
                $node->stmts[$key] = $stmt;

                // return the changed node to override it
                return $node;
            }
        }

        // no constructor defined
        return $node;
    }
}
