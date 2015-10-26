<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ComposerLoader.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class ComposerLoader implements LoaderInterface
{
    // TODO: not hard coded  & relative
    const PACKAGE_PATH = 'vendor/';

    /**
     * @var DirectoryTraverser
     */
    protected $traverser;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param DirectoryTraverser $traverser
     * @param Cache              $cache
     */
    public function __construct(DirectoryTraverser $traverser, Cache $cache)
    {
        $this->traverser = $traverser;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function loadRuleSet($lock)
    {
        $composer = $this->getComposerObject($lock);
        $packages = $this->getComposerPackages($composer);

        $ruleSet = new RuleSet();
        foreach ($packages as $i => $package) {
            $packageRuleSet = $this->loadPackageRuleSet($package);
            if (null !== $packageRuleSet) {
                $ruleSet->merge($packageRuleSet);
            }
        }

        return $ruleSet;
    }

    /**
     * @param $lock
     *
     * @return mixed
     *
     * @throws CouldNotLoadRuleSetException
     */
    private function getComposerObject($lock)
    {
        if (!is_file($lock)) {
            throw new CouldNotLoadRuleSetException(sprintf(
                'composer.lock file "%s" does not exist',
                $lock
            ));
        }

        $file = new SplFileInfo($lock, null, null);
        $composer = json_decode($file->getContents());

        if (null === $composer || !isset($composer->packages)) {
            throw new CouldNotLoadRuleSetException(sprintf(
               'composer.lock file "$s" is not valid.',
                $lock
            ));
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
     * @return RuleSet|null
     */
    private function loadPackageRuleSet(\stdClass $package)
    {
        $ruleSet = null;
        $key = $this->generatePackageKey($package);

        if ($this->cache->has($key)) {
            $ruleSet = $this->cache->getCachedRuleSet($key);
        } elseif (is_dir($path = $this->getPackagePath($package))) {
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
