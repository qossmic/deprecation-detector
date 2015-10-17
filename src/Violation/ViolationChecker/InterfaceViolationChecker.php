<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class InterfaceViolationChecker implements ViolationCheckerInterface
{
    /**
     * @var RuleSet
     */
    private $ruleSet;

    /**
     * @param RuleSet $ruleSet
     */
    public function __construct(RuleSet $ruleSet)
    {
        $this->ruleSet = $ruleSet;
    }

    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo)
    {
        $violations = array();

        foreach ($phpFileInfo->interfaceUsages() as $interfaceUsageGroup) {
            foreach ($interfaceUsageGroup as $interfaceUsage) {
                if ($this->ruleSet->hasInterface($interfaceUsage->name())) {
                    $violations[] = new Violation(
                        $interfaceUsage,
                        $phpFileInfo,
                        $this->ruleSet->getInterface($interfaceUsage->name())->comment()
                    );
                }
            }
        }

        return $violations;
    }
}
