<?php

namespace SensioLabs\DeprecationDetector\RuleSet;

use Pimple\Container;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\ProgressEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Traverser.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class Traverser
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param Container       $container
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(Container $container, EventDispatcher $eventDispatcher)
    {
        $this->container = $container;
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
        $files = $this->container['finder.php_deprecation']->in($path);

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
