<?php

namespace SensioLabs\DeprecationDetector\Configuration;

class Configuration
{
    /**
     * @var string
     */
    private $ruleSet;

    /**
     * @var string
     */
    private $containerPath;

    /**
     * @var bool
     */
    private $noCache;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $filterMethodCalls;

    /**
     * @var bool
     */
    private $fail;

    /**
     * @var bool
     */
    private $verbose;

    /**
     * @param string $ruleSet
     * @param string $containerPath
     * @param bool   $noCache
     * @param string $cacheDir
     * @param string $filterMethodCalls
     * @param bool   $fail
     * @param bool   $verbose
     */
    public function __construct(
        $ruleSet,
        $containerPath,
        $noCache,
        $cacheDir,
        $filterMethodCalls,
        $fail,
        $verbose)
    {
        $this->ruleSet = $ruleSet;
        $this->containerPath = $containerPath;
        $this->noCache = $noCache;
        $this->cacheDir = $cacheDir;
        $this->filterMethodCalls = $filterMethodCalls;
        $this->fail = $fail;
        $this->verbose = $verbose;
    }

    public function overrideConfiguration()
    {
    }

    /**
     * @return string
     */
    public function ruleSet()
    {
        return $this->ruleSet;
    }

    /**
     * @return string
     */
    public function containerPath()
    {
        return $this->containerPath;
    }

    /**
     * @return bool
     */
    public function useCachedRuleSet()
    {
        return $this->noCache;
    }

    /**
     * @return string
     */
    public function ruleSetCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @return string
     */
    public function filteredMethodCalls()
    {
        return $this->filterMethodCalls;
    }

    /**
     * @return bool
     */
    public function failOnDeprecation()
    {
        return $this->fail;
    }

    /**
     * @return bool
     */
    public function isVerbose()
    {
        return $this->verbose;
    }
}
