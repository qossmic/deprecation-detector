<?php

namespace SensioLabs\DeprecationDetector\RuleSet;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;
use SensioLabs\DeprecationDetector\DeprecationCollectionInterface;

class RuleSet implements DeprecationCollectionInterface
{
    /**
     * @var array
     */
    private $classDeprecations;

    /**
     * @var array
     */
    private $interfaceDeprecations;

    /**
     * @var array
     */
    private $methodDeprecations;

    /**
     * @param array $classDeprecations
     * @param array $interfaceDeprecations
     * @param array $methodDeprecations
     */
    public function __construct(array $classDeprecations = array(),
                                array $interfaceDeprecations = array(),
                                array $methodDeprecations = array())
    {
        $this->classDeprecations = $classDeprecations;
        $this->interfaceDeprecations = $interfaceDeprecations;
        $this->methodDeprecations = $methodDeprecations;
    }

    /**
     * @param DeprecationCollectionInterface $collection
     */
    public function merge(DeprecationCollectionInterface $collection)
    {
        $this->classDeprecations = array_merge($this->classDeprecations(), $collection->classDeprecations());
        $this->interfaceDeprecations = array_merge($this->interfaceDeprecations(), $collection->interfaceDeprecations());
        $this->methodDeprecations = array_merge($this->methodDeprecations(), $collection->methodDeprecations());
    }

    /**
     * @return ClassDeprecation[]
     */
    public function classDeprecations()
    {
        return $this->classDeprecations;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function hasClass($class)
    {
        return isset($this->classDeprecations[$class]);
    }

    /**
     * @param $class
     *
     * @return ClassDeprecation|null
     */
    public function getClass($class)
    {
        if (!$this->hasClass($class)) {
            return;
        }

        return $this->classDeprecations[$class];
    }

    /**
     * @return InterfaceDeprecation[]
     */
    public function interfaceDeprecations()
    {
        return $this->interfaceDeprecations;
    }

    /**
     * @param string $interface
     *
     * @return bool
     */
    public function hasInterface($interface)
    {
        return isset($this->interfaceDeprecations[$interface]);
    }

    /**
     * @param string $interface
     *
     * @return InterfaceDeprecation|null
     */
    public function getInterface($interface)
    {
        if (!$this->hasInterface($interface)) {
            return;
        }

        return $this->interfaceDeprecations[$interface];
    }

    /**
     * @return MethodDeprecation[]
     */
    public function methodDeprecations()
    {
        return $this->methodDeprecations;
    }

    /**
     * @param string $class
     * @param string $method
     *
     * @return bool
     */
    public function hasMethod($method, $class)
    {
        return isset($this->methodDeprecations[$class][$method]);
    }

    /**
     * @param $method
     * @param $class
     *
     * @return MethodDeprecation|null
     */
    public function getMethod($method, $class)
    {
        if (!$this->hasMethod($method, $class)) {
            return;
        }

        return $this->methodDeprecations[$class][$method];
    }
}
