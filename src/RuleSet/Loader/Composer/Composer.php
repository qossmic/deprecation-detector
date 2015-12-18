<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader\Composer;

class Composer
{
    /**
     * @var Package[]
     */
    private $packages;

    /**
     * @var Package[]
     */
    private $devPackages;

    /**
     * @var bool
     */
    private $includeDev;

    /**
     * @param Package[] $packages
     * @param Package[] $devPackages
     * @param bool $includeDev
     */
    public function __construct(array $packages, array $devPackages, $includeDev)
    {
        $this->packages = $packages;
        $this->devPackages = $devPackages;
        $this->includeDev = $includeDev;
    }

    /**
     * @return Package[]
     */
    public function getPackages()
    {
        if (true === $this->includeDev) {
            return array_merge($this->packages, $this->devPackages);
        }

        return $this->packages;
    }
}
