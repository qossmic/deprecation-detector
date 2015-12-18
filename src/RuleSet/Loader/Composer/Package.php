<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader\Composer;

class Package
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @param string $name
     * @param string $version
     */
    private function __construct($name, $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * @param array $package
     *
     * @return Package
     */
    public static function fromArray(array $package)
    {
        return new self(
            $package['name'],
            $package['version']
        );
    }

    /**
     * @return string
     */
    public function generatePackageKey()
    {
        return str_replace('/', '_', $this->name).'_'.$this->version;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public function getPackagePath($prefix)
    {
        return $prefix.$this->name;
    }
}
