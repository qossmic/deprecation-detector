<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\AncestorResolver;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class MethodViolationChecker implements ViolationCheckerInterface
{
    /**
     * @var RuleSet
     */
    protected $ruleSet;

    /**
     * @var AncestorResolver
     */
    protected $ancestorResolver;

    /**
     * @param RuleSet          $ruleSet
     * @param AncestorResolver $ancestorResolver
     */
    public function __construct(RuleSet $ruleSet, AncestorResolver $ancestorResolver)
    {
        $this->ruleSet = $ruleSet;
        $this->ancestorResolver = $ancestorResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo)
    {
        $violations = array();

        foreach ($phpFileInfo->methodUsages() as $methodUsage) {
            $className = $methodUsage->className();

            if ($this->ruleSet->hasMethod($methodUsage->name(), $className)) {
                $violations[] = new Violation(
                    $methodUsage,
                    $phpFileInfo,
                    $this->ruleSet->getMethod($methodUsage->name(), $className)->comment()
                );
            }

            $ancestors = $this->ancestorResolver->getClassAncestors($phpFileInfo, $methodUsage->className());

            foreach ($ancestors as $ancestor) {
                if ($this->ruleSet->hasMethod($methodUsage->name(), $ancestor)) {
                    $violations[] = new Violation(
                        new MethodUsage(
                            $methodUsage->name(),
                            $ancestor,
                            $methodUsage->getLineNumber(),
                            $methodUsage->isStatic()
                        ),
                        $phpFileInfo,
                        $this->ruleSet->getMethod($methodUsage->name(), $ancestor)->comment()
                    );
                }
            }
        }

        return $violations;
    }
}
