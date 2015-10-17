<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

use SensioLabs\DeprecationDetector\ProgressEvent;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileLoader.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class FileLoader
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @see LoaderInterface
     */
    public function loadRuleSet($path)
    {
        $this->eventDispatcher->dispatch(
            ProgressEvent::RULESET,
            new ProgressEvent(0, 1)
        );

        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Rule set file "%s" does not exist.', $path));
        }

        $file = new SplFileInfo($path, null, null);
        $ruleSet = unserialize($file->getContents());

        if (!$ruleSet instanceof RuleSet) {
            throw new \RuntimeException('Rule set file is not valid.');
        }

        $this->eventDispatcher->dispatch(
            ProgressEvent::RULESET,
            new ProgressEvent(1, 1)
        );

        return $ruleSet;
    }
}
