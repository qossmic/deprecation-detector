<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class ClassViolationChecker implements ViolationCheckerInterface
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

        foreach ($phpFileInfo->classUsages() as $classUsage) {
            if ($this->ruleSet->hasClass($classUsage->name())) {
                $violations[] = new Violation(
                    $classUsage,
                    $phpFileInfo,
                    $this->ruleSet->getClass($classUsage->name())->comment()
                );
            }
        }

        return $violations;
    }
}
