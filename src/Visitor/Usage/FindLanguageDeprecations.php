<?php

namespace SensioLabs\DeprecationDetector\Visitor\Usage;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
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
        if ($node instanceof Node\Expr\AssignRef && $node->expr instanceof Node\Expr\New_) {
            $this->phpFileInfo->addDeprecatedLanguageUsage(
                new DeprecatedLanguageUsage('Assigning the return value of new by reference is now deprecated.', 'Since PHP 5.3 use normal assignment instead.', $node->getLine())
            );
        }

        if ($node instanceof Node\Stmt\Class_) {
            $method = $node->getMethod($node->name);
            if ($method instanceof Node\Stmt\ClassMethod) {
                $this->phpFileInfo->addDeprecatedLanguageUsage(
                    new DeprecatedLanguageUsage(
                        'PHP4 constructor',
                        'Since PHP 7.0, use __construct() instead.',
                        $method->getLine()
                    )
                );
            }
        }

        if ($node instanceof Node\Arg) {
            if (true === $node->byRef) {
                $this->phpFileInfo->addDeprecatedLanguageUsage(
                    new DeprecatedLanguageUsage(
                        'call-time pass-by-reference',
                        'Since PHP 5.3 and removed in PHP 5.4',
                        $node->getLine()
                    )
                );
            }
        }
    }
}
