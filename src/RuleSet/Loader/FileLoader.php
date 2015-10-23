<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

use SensioLabs\DeprecationDetector\EventListener\ProgressEvent;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileLoader.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class FileLoader implements LoaderInterface
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
     * {@inheritdoc}
     */
    public function loadRuleSet($path)
    {
        $this->eventDispatcher->dispatch(
            ProgressEvent::START_RULESET,
            new ProgressEvent(0, 1)
        );

        if (!is_file($path)) {
            throw new CouldNotLoadRuleSetException(sprintf(
                'Ruleset "%s" does not exist, aborting.',
                $path
                )
            );
        }

        $file = new SplFileInfo($path, null, null);
        $ruleSet = unserialize($file->getContents());

        if (!$ruleSet instanceof RuleSet) {
            throw new CouldNotLoadRuleSetException(sprintf(
                'Ruleset "$s" is invalid, aborting.',
                    $path
                )
            );
        }

        $this->eventDispatcher->dispatch(
            ProgressEvent::GENERATED_RULESET,
            new ProgressEvent(1, 1)
        );

        return $ruleSet;
    }
}
