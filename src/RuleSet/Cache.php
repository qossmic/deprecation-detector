<?php

namespace SensioLabs\DeprecationDetector\RuleSet;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Cache.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class Cache
{
    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     * @param bool|true  $enabled
     * @param string     $cacheDir
     */
    public function __construct(Filesystem $filesystem, $enabled = true, $cacheDir = '.rules/')
    {
        $this->filesystem = $filesystem;
        $this->enabled = $enabled;
        $this->setCacheDir($cacheDir);
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = (substr($cacheDir, -1) === '/' ? $cacheDir : $cacheDir.'/');
    }

    /**
     * call to disable the cache.
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->enabled && $this->filesystem->exists($this->cacheDir.$key);
    }

    /**
     * @param string       $key
     * @param RuleSet|null $ruleSet
     */
    public function cacheRuleSet($key, $ruleSet)
    {
        if ($this->enabled) {
            $this->filesystem->dumpFile($this->cacheDir.$key, serialize($ruleSet));
        }
    }

    /**
     * @param string $key
     *
     * @return RuleSet|null
     */
    public function getCachedRuleSet($key)
    {
        if (!$this->enabled || !$this->filesystem->exists($this->cacheDir.$key)) {
            return;
        }

        $file = new SplFileInfo($this->cacheDir.$key, null, null);

        return unserialize($file->getContents());
    }
}
