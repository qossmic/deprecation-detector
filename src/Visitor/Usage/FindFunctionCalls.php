<?php

namespace SensioLabs\DeprecationDetector\Visitor\Usage;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage;
use SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface;

class FindFunctionCalls extends NodeVisitorAbstract implements ViolationVisitorInterface
{
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
        if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name) {
            $this->phpFileInfo->addFunctionUsage(
                new FunctionUsage($node->name->toString(), $node->getLine())
            );
        }
    }
}
