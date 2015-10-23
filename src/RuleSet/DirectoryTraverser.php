<?php

namespace SensioLabs\DeprecationDetector\RuleSet;

use Pimple\Container;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Parser\DeprecationParser;
use SensioLabs\DeprecationDetector\ProgressEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Traverser.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class DirectoryTraverser
{
    /**
     * @var DeprecationParser
     */
    private $deprecationParser;

    /**
     * @param DeprecationParser $deprecationParser
     * @param EventDispatcher   $eventDispatcher
     */
    public function __construct(DeprecationParser $deprecationParser, EventDispatcher $eventDispatcher)
    {
        $this->deprecationParser = $deprecationParser;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $path
     * @param $quite
     *
     * @return null|RuleSet
     */
    public function traverse($path, $quite = false)
    {
        $files = $this->deprecationParser->in($path);

        $ruleSet = new RuleSet();
        $hasDeprecations = false;
        if (!$quite && null !== $this->eventDispatcher) {
            $total = count($files);
            $this->eventDispatcher->dispatch(
                ProgressEvent::RULESET,
                new ProgressEvent(0, $total)
            );
        }

        foreach ($files as $i => $file) {
            /** @var PhpFileInfo $file */
            if ($file->hasDeprecations()) {
                $ruleSet->merge($file);
                $hasDeprecations = true;
            }

            if (!$quite && null !== $this->eventDispatcher) {
                $this->eventDispatcher->dispatch(
                    ProgressEvent::RULESET,
                    new ProgressEvent(++$i, $total)
                );
            }
        }

        return ($hasDeprecations ? $ruleSet : null);
    }
}
