<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ResolverInterface;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\TableScope;
use SensioLabs\DeprecationDetector\Visitor\StaticAnalysisVisitorInterface;

class SymbolTableVariableResolverVisitor extends NodeVisitorAbstract implements StaticAnalysisVisitorInterface
{
    /**
     * @var PhpFileInfo
     */
    protected $phpFileInfo;

    /**
     * @var SymbolTable
     */
    private $table;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     * @param SymbolTable       $table
     */
    public function __construct(ResolverInterface $resolver, SymbolTable $table)
    {
        $this->resolver = $resolver;
        $this->table = $table;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike) {
            if ($node instanceof Node\Stmt\Class_ && $node->isAnonymous()) {
                return;
            }

            $this->table->enterScope(new TableScope(TableScope::CLASS_LIKE_SCOPE));
            $this->table->setSymbol('this', $node->namespacedName->toString());
        }

        if ($node instanceof Node\Stmt\ClassMethod) {
            $this->table->enterScope(new TableScope(TableScope::CLASS_METHOD_SCOPE));
        }

        if ($node instanceof Node\Stmt\Function_) {
            $this->table->enterScope(new TableScope(TableScope::FUNCTION_SCOPE));
        }

        $this->resolver->resolveVariableType($node);

        return;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_
                || $node instanceof Node\Stmt\Interface_
                || $node instanceof Node\Stmt\Trait_) {
            if ($node instanceof Node\Stmt\Class_ && $node->isAnonymous()) {
                return;
            }

            $this->table->leaveScope();
        }

        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Stmt\Function_) {
            $this->table->leaveScope();
        }
    }
}
