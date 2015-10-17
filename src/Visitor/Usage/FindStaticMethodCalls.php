<?php

namespace SensioLabs\DeprecationDetector\Visitor\Usage;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface;

class FindStaticMethodCalls extends NodeVisitorAbstract implements ViolationVisitorInterface
{
    /**
     * @var string
     */
    protected $parentName;

    /**
     * @var PhpFileInfo
     */
    protected $phpFileInfo;

    /**
     * @param PhpFileInfo $phpFileInfo
     *
     * @return $this
     */
    public function setPhpFileInfo(PhpFileInfo $phpFileInfo)
    {
        $this->phpFileInfo = $phpFileInfo;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike) {
            if (isset($node->namespacedName)) {
                $this->parentName = $node->namespacedName->toString();
            } else {
                $this->parentName = $node->name;
            }
        }

        if ($node instanceof Node\Expr\StaticCall) {
            // skips concat method names like $twig->{'get'.ucfirst($type)}()
            if ($node->name instanceof Node\Expr\BinaryOp\Concat) {
                return;
            }

            // skips variable methods like $definition->$method
            if (!is_string($node->name)) {
                return;
            }

            $className = null;
            if ($node->class instanceof Node\Name) {
                if ('parent' == $node->class->toString()) {
                    $className = $this->parentName;
                } else {
                    $className = $node->class->toString();
                }
            } elseif ($node->class instanceof Node\Expr\Variable) {
                if (null === $className = $node->class->getAttribute('guessedType')) {
                    return;
                }
            }

            $methodUsage = new MethodUsage($node->name, $className, $node->getLine(), true);
            $this->phpFileInfo->addMethodUsage($methodUsage);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike) {
            $this->parentName = null;
        }
    }
}
