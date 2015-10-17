<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\AncestorResolver;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class MethodDefinitionViolationChecker implements ViolationCheckerInterface
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

        foreach ($phpFileInfo->methodDefinitions() as $methodDefinition) {
            $ancestors = $this->ancestorResolver->getClassAncestors($phpFileInfo, $methodDefinition->parentName());

            foreach ($ancestors as $ancestor) {
                if ($this->ruleSet->hasMethod($methodDefinition->name(), $ancestor)) {
                    $violations[] = new Violation(
                        $methodDefinition,
                        $phpFileInfo,
                        $this->ruleSet->getMethod($methodDefinition->name(), $ancestor)->comment()
                    );
                }
            }
        }

        return $violations;
    }
}
