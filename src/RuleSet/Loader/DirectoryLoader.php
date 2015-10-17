<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\Traverser;

/**
 * Class DirectoryLoader.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class DirectoryLoader implements LoaderInterface
{
    /**
     * @var Traverser
     */
    protected $traverser;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param Traverser $traverser
     * @param Cache     $cache
     */
    public function __construct(Traverser $traverser, Cache $cache)
    {
        $this->traverser = $traverser;
        $this->cache = $cache;
    }

    /**
     * @see LoaderInterface
     */
    public function loadRuleSet($path)
    {
        $key = $this->generateDirectoryKey($path);

        if ($this->cache->has($key)) {
            return $this->cache->getCachedRuleSet($key);
        }

        $ruleSet = $this->traverser->traverse($path);
        $this->cache->cacheRuleSet($key, $ruleSet);

        return $ruleSet;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function generateDirectoryKey($path)
    {
        return str_replace('/', '_', $path);
    }
}
