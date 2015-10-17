<?php

namespace SensioLabs\DeprecationDetector\Detector;

use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\ProgressEvent;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ViolationDetector {

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var ViolationCheckerInterface
     */
    private $violationChecker;

    public function __construct(EventDispatcherInterface $eventDispatcher, ViolationCheckerInterface $violationChecker)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->violationChecker = $violationChecker;
    }

    /**
     * @param RuleSet             $ruleSet
     * @param ParsedPhpFileFinder $files
     *
     * @return Violation[]
     */
    public function getViolations(RuleSet $ruleSet, ParsedPhpFileFinder $files)
    {
        $total = count($files);
        $this->eventDispatcher->dispatch(
            ProgressEvent::CHECKER,
            new ProgressEvent(0, $total)
        );

        $result = array();
        foreach ($files as $i => $file) {
            $result = array_merge($result, $this->violationChecker->check($file, $ruleSet));

            $this->eventDispatcher->dispatch(
                ProgressEvent::CHECKER,
                new ProgressEvent(++$i, $total)
            );
        }

        return $result;
    }

}