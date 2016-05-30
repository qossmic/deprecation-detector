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
    private $useCachedRuleSet;

    /**
     * @var string
     */
    private $ruleSetCacheDir;

    /**
     * @var string
     */
    private $filterMethodCalls;

    /**
     * @var bool
     */
    private $failOnDeprecation;

    /**
     * @var bool
     */
    private $verbose;

    /**
     * @var string
     */
    private $logHtml;

    /**
     * @var string
     */
    private $output;

    /**
     * @param string $ruleSet
     * @param string $containerPath
     * @param bool   $noCache
     * @param string $cacheDir
     * @param string $filterMethodCalls
     * @param bool   $fail
     * @param bool   $verbose
     * @param string $logHtml
     * @param string $output
     */
    public function __construct(
        $ruleSet,
        $containerPath,
        $noCache,
        $cacheDir,
        $filterMethodCalls,
        $fail,
        $verbose,
        $logHtml,
        $output)
    {
        $this->ruleSet = $ruleSet;
        $this->containerPath = $containerPath;
        $this->useCachedRuleSet = $noCache;
        $this->ruleSetCacheDir = $cacheDir;
        $this->filterMethodCalls = $filterMethodCalls;
        $this->failOnDeprecation = $fail;
        $this->verbose = $verbose;
        $this->logHtml = $logHtml;
        $this->output = $output;
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
        return $this->useCachedRuleSet;
    }

    /**
     * @return string
     */
    public function ruleSetCacheDir()
    {
        return $this->ruleSetCacheDir;
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
        return $this->failOnDeprecation;
    }

    /**
     * @return bool
     */
    public function isVerbose()
    {
        return $this->verbose;
    }

    public function logHtml()
    {
        return $this->logHtml;
    }

    /**
     * @return bool
     */
    public function isSimpleOutput() {
        return $this->output === 'simple';
    }
}
