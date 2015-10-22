<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Configuration;

class Configuration
{
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
     * @param string $containerPath
     * @param bool   $noCache
     * @param string $cacheDir
     * @param string $filterMethodCalls
     * @param bool   $fail
     */
    public function __construct(
        $containerPath,
        $noCache,
        $cacheDir,
        $filterMethodCalls,
        $fail)
    {
        $this->containerPath = $containerPath;
        $this->noCache = $noCache;
        $this->cacheDir = $cacheDir;
        $this->filterMethodCalls = $filterMethodCalls;
        $this->fail = $fail;
    }

    public function overrideConfiguration()
    {
        /* @TODO */
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
    public function noCache()
    {
        return $this->noCache;
    }

    /**
     * @return string
     */
    public function cacheDir()
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
    public function fail()
    {
        return $this->fail;
    }
}
