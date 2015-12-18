<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader\Composer;

use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerException;
use SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException;
use SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;

class ComposerLoader implements LoaderInterface
{
    // TODO: not hard coded  & relative
    const PACKAGE_PATH = 'vendor/';

    /**
     * @var DirectoryTraverser
     */
    private $traverser;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var ComposerFactory
     */
    private $factory;

    /**
     * @param DirectoryTraverser $traverser
     * @param Cache              $cache
     * @param ComposerFactory    $factory
     */
    public function __construct(DirectoryTraverser $traverser, Cache $cache, ComposerFactory $factory)
    {
        $this->traverser = $traverser;
        $this->cache = $cache;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadRuleSet($lock)
    {
        try {
            $composer = $this->factory->fromLock($lock);
        } catch (ComposerException $e) {
            throw new CouldNotLoadRuleSetException($e->getMessage());
        }

        $ruleSet = new RuleSet();
        foreach ($composer->getPackages() as $package) {
            $ruleSet->merge($this->loadPackageRuleSet($package));
        }

        return $ruleSet;
    }

    /**
     * @param Package $package
     *
     * @return RuleSet
     */
    private function loadPackageRuleSet(Package $package)
    {
        $ruleSet = new RuleSet();
        $key = $package->generatePackageKey();

        if ($this->cache->has($key)) {
            $ruleSet = $this->cache->getCachedRuleSet($key);
        } elseif (is_dir($path = $package->getPackagePath(self::PACKAGE_PATH))) {
            $this->traverser->reset();
            $ruleSet = $this->traverser->traverse($path);
            $this->cache->cacheRuleSet($key, $ruleSet);
        } else {
            // there is no vendor package in the given path
        }

        return $ruleSet;
    }
}
