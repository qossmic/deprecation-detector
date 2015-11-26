<?php

namespace SensioLabs\DeprecationDetector\Visitor\Usage;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage\AssignReferenceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage\OldConstructorUsage;
use SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface;

class FindLanguageDeprecations extends NodeVisitorAbstract implements ViolationVisitorInterface
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
        if ($node instanceof Node\Expr\AssignRef) {
            $this->phpFileInfo->addDeprecatedLanguageUsage(
                new AssignReferenceUsage($node->getLine())
            );
        }

        if ($node instanceof Node\Stmt\Class_) {
            $method = $node->getMethod($node->name);
            if ($method instanceof Node\Stmt\ClassMethod) {
                $this->phpFileInfo->addDeprecatedLanguageUsage(
                    new OldConstructorUsage($method->getLine())
                );
            }
        }
    }
}
