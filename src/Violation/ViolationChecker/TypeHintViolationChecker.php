<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class TypeHintViolationChecker implements ViolationCheckerInterface
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

        foreach ($phpFileInfo->typeHintUsages() as $typeHintUsage) {
            $isClass = $this->ruleSet->hasClass($typeHintUsage->name());

            if ($isClass || $this->ruleSet->hasInterface($typeHintUsage->name())) {
                $usage = $isClass ?
                    new ClassUsage($typeHintUsage->name(), $typeHintUsage->getLineNumber()) :
                    new InterfaceUsage($typeHintUsage->name(), '', $typeHintUsage->getLineNumber());

                $comment = $isClass ?
                    $this->ruleSet->getClass($typeHintUsage->name())->comment() :
                    $this->ruleSet->getInterface($typeHintUsage->name())->comment();

                $violations[] = new Violation(
                    $usage,
                    $phpFileInfo,
                    $comment
                );
            }
        }

        return $violations;
    }
}
