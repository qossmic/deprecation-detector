<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class SuperTypeViolationChecker implements ViolationCheckerInterface
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

        foreach ($phpFileInfo->superTypeUsages() as $superTypeUsage) {
            if ($this->ruleSet->hasClass($superTypeUsage->name())) {
                $violations[] = new Violation(
                    $superTypeUsage,
                    $phpFileInfo,
                    $this->ruleSet->getClass($superTypeUsage->name())->comment()
                );
            }
        }

        return $violations;
    }
}
