<?php

namespace SensioLabs\DeprecationDetector\Visitor\Usage;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface;

class FindInterfaces extends NodeVisitorAbstract implements ViolationVisitorInterface
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
        if ($node instanceof Node\Stmt\Class_) {
            if ($node->isAnonymous()) {
                return;
            }

            $phpFileInfo = $this->phpFileInfo;
            array_map(function (Node\Name $interface) use ($node, $phpFileInfo) {
                $interfaceUsage = new InterfaceUsage(
                    $interface->toString(),
                    $node->namespacedName->toString(),
                    $node->getLine()
                );

                $phpFileInfo->addInterfaceUsage($interfaceUsage);
            }, $node->implements);
        }
    }
}
