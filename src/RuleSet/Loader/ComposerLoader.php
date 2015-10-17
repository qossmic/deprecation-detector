<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

use SensioLabs\DeprecationDetector\ProgressEvent;
use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\Traverser;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ComposerLoader.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class ComposerLoader
{
    // TODO: not hard coded  & relative
    const PACKAGE_PATH = 'vendor/';

    /**
     * @var Traverser
     */
    protected $traverser;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param Traverser       $traverser
     * @param Cache           $cache
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(Traverser $traverser, Cache $cache, EventDispatcher $eventDispatcher)
    {
        $this->traverser = $traverser;
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $lock
     *
     * @return RuleSet
     */
    public function loadRuleSet($lock)
    {
        $composer = $this->getComposerObject($lock);
        $packages = $this->getComposerPackages($composer);

        $total = count($packages);
        $this->eventDispatcher->dispatch(
            ProgressEvent::RULESET,
            new ProgressEvent(0, $total)
        );

        $ruleSet = new RuleSet();
        foreach ($packages as $i => $package) {
            $packageRuleSet = $this->loadPackageRuleSet($package);
            if (null !== $packageRuleSet) {
                $ruleSet->merge($packageRuleSet);
            }

            $this->eventDispatcher->dispatch(
                ProgressEvent::RULESET,
                new ProgressEvent(++$i, $total)
            );
        }

        return $ruleSet;
    }

    /**
     * @param $lock
     *
     * @return mixed
     *
     * @throws \RunTimeException
     */
    private function getComposerObject($lock)
    {
        if (!is_file($lock)) {
            throw new \RuntimeException('Lock file does not exist.');
        }

        $file = new SplFileInfo($lock, null, null);
        $composer = json_decode($file->getContents());

        if (null === $composer || !isset($composer->packages)) {
            throw new \RuntimeException('Lock file is not valid.');
        }

        return $composer;
    }

    /**
     * @param $composer
     * @param bool|false $noDev
     *
     * @return array
     */
    private function getComposerPackages(\stdClass $composer, $noDev = false)
    {
        $packages = $composer->packages;

        if (!$noDev && isset($composer->{'packages-dev'})) {
            $packages = array_merge($packages, $composer->{'packages-dev'});
        }

        return $packages;
    }

    /**
     * @param $package
     *
     * @return RuleSet
     */
    private function loadPackageRuleSet(\stdClass $package)
    {
        $key = $this->generatePackageKey($package);

        if ($this->cache->has($key)) {
            $ruleSet = $this->cache->getCachedRuleSet($key);
        } else {
            $path = $this->getPackagePath($package);
            $ruleSet = $this->traverser->traverse($path, true);
            $this->cache->cacheRuleSet($key, $ruleSet);
        }

        return $ruleSet;
    }

    /**
     * @param $package
     *
     * @return string
     */
    private function generatePackageKey(\stdClass $package)
    {
        return str_replace('/', '_', $package->name).'_'.md5(serialize($package));
    }

    /**
     * @param $package
     *
     * @return string
     */
    private function getPackagePath(\stdClass $package)
    {
        return self::PACKAGE_PATH.$package->name;
    }
}
