<?php

namespace SensioLabs\DeprecationDetector\Visitor\Usage;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage;
use SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface;

class FindArguments extends NodeVisitorAbstract implements ViolationVisitorInterface
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
        if ($node instanceof Node\Param && $node->type instanceof Node\Name) {
            $typeHintUsage = new TypeHintUsage($node->type->toString(), $node->getLine());
            $this->phpFileInfo->addTypeHintUsage($typeHintUsage);
        }
    }
}
