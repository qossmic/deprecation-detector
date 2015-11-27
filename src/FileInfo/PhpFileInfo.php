<?php

namespace SensioLabs\DeprecationDetector\FileInfo;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage;
use Symfony\Component\Finder\SplFileInfo;

class PhpFileInfo extends SplFileInfo implements DeprecationCollectionInterface
{
    /**
     * @var ClassUsage[]
     */
    protected $classUsages = array();

    /**
     * @var DeprecatedLanguageUsage[]
     */
    protected $deprecatedLanguageUsages = array();

    /**
     * @var InterfaceUsage[]
     */
    protected $interfaceUsages = array();

    /**
     * @var array
     */
    protected $superTypeUsages = array();

    /**
     * @var MethodUsage[]
     */
    protected $methodUsages = array();

    /**
     * @var TypeHintUsage[]
     */
    protected $typeHintUsages = array();

    /**
     * @var MethodDeprecation[]
     */
    protected $classDeprecations = array();

    /**
     * @var MethodDeprecation[]
     */
    protected $interfaceDeprecations = array();

    /**
     * @var MethodDeprecation[]
     */
    protected $methodDeprecations = array();

    /**
     * @var array
     */
    protected $methodDefinitions = array();

    /**
     * @param SplFileInfo $file
     *
     * @return self
     */
    public static function create(SplFileInfo $file)
    {
        return new static($file->getPathname(), $file->getRelativePath(), $file->getRelativePathname());
    }

    /**
     * @param ClassUsage $class
     */
    public function addClassUsage(ClassUsage $class)
    {
        $this->classUsages[] = $class;
    }

    /**
     * @return ClassUsage[]
     */
    public function classUsages()
    {
        return $this->classUsages;
    }

    /**
     * @param InterfaceUsage $interface
     */
    public function addInterfaceUsage(InterfaceUsage $interface)
    {
        if (!isset($this->interfaceUsages[$interface->className()])) {
            $this->interfaceUsages[$interface->className()] = array();
        }

        $this->interfaceUsages[$interface->className()][] = $interface;
    }

    /**
     * @return InterfaceUsage[]
     */
    public function interfaceUsages()
    {
        return $this->interfaceUsages;
    }

    /**
     * @param $className
     *
     * @return bool
     */
    public function hasInterfaceUsageByClass($className)
    {
        if (!isset($this->interfaceUsages[$className])) {
            return false;
        }

        return count($this->interfaceUsages[$className]) > 0;
    }

    /**
     * @param $className
     *
     * @return InterfaceUsage[]
     */
    public function getInterfaceUsageByClass($className)
    {
        if (!isset($this->interfaceUsages[$className])) {
            return array();
        }

        return $this->interfaceUsages[$className];
    }

    /**
     * @param SuperTypeUsage $superTypeUsage
     */
    public function addSuperTypeUsage(SuperTypeUsage $superTypeUsage)
    {
        $this->superTypeUsages[$superTypeUsage->className()] = $superTypeUsage;
    }

    /**
     * @return SuperTypeUsage[]
     */
    public function superTypeUsages()
    {
        return $this->superTypeUsages;
    }

    /**
     * @param $className
     *
     * @return bool
     */
    public function hasSuperTypeUsageByClass($className)
    {
        return isset($this->superTypeUsages[$className]);
    }

    /**
     * @param $className
     *
     * @return SuperTypeUsage|null
     */
    public function getSuperTypeUsageByClass($className)
    {
        if (!isset($this->superTypeUsages[$className])) {
            return;
        }

        return $this->superTypeUsages[$className];
    }

    /**
     * @param MethodUsage $methodUsage
     */
    public function addMethodUsage(MethodUsage $methodUsage)
    {
        $this->methodUsages[] = $methodUsage;
    }

    /**
     * @return MethodUsage[]
     */
    public function methodUsages()
    {
        return $this->methodUsages;
    }

    /**
     * @param TypeHintUsage $typeHintUsage
     */
    public function addTypeHintUsage(TypeHintUsage $typeHintUsage)
    {
        $this->typeHintUsages[] = $typeHintUsage;
    }

    /**
     * @return TypeHintUsage[]
     */
    public function typeHintUsages()
    {
        return $this->typeHintUsages;
    }

    /**
     * @param MethodDefinition $methodDefinition
     */
    public function addMethodDefinition(MethodDefinition $methodDefinition)
    {
        $this->methodDefinitions[] = $methodDefinition;
    }

    /**
     * @return MethodDefinition[]
     */
    public function methodDefinitions()
    {
        return $this->methodDefinitions;
    }

    /**
     * @param ClassDeprecation $classDeprecation
     */
    public function addClassDeprecation(ClassDeprecation $classDeprecation)
    {
        $this->classDeprecations[$classDeprecation->name()] = $classDeprecation;
    }

    /**
     * @return ClassDeprecation[]
     */
    public function classDeprecations()
    {
        return $this->classDeprecations;
    }

    /**
     * @return bool
     */
    public function hasClassDeprecations()
    {
        return count($this->classDeprecations()) > 0;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function hasClassDeprecation($className)
    {
        return isset($this->classDeprecations[$className]);
    }

    /**
     * @param string $className
     *
     * @return ClassDeprecation
     */
    public function getClassDeprecation($className)
    {
        return ($this->hasClassDeprecation($className) ? $this->classDeprecations[$className] : null);
    }

    /**
     * @param InterfaceDeprecation $interfaceDeprecation
     */
    public function addInterfaceDeprecation(InterfaceDeprecation $interfaceDeprecation)
    {
        $this->interfaceDeprecations[$interfaceDeprecation->name()] = $interfaceDeprecation;
    }

    /**
     * @return InterfaceDeprecation[]
     */
    public function interfaceDeprecations()
    {
        return $this->interfaceDeprecations;
    }

    /**
     * @return bool
     */
    public function hasInterfaceDeprecations()
    {
        return count($this->interfaceDeprecations()) > 0;
    }

    /**
     * @param string $interfaceName
     *
     * @return bool
     */
    public function hasInterfaceDeprecation($interfaceName)
    {
        return isset($this->interfaceDeprecations[$interfaceName]);
    }

    /**
     * @param string $interfaceName
     *
     * @return ClassDeprecation
     */
    public function getInterfaceDeprecation($interfaceName)
    {
        return ($this->hasInterfaceDeprecation($interfaceName) ? $this->interfaceDeprecations[$interfaceName] : null);
    }

    /**
     * @param MethodDeprecation $methodDeprecation
     */
    public function addMethodDeprecation(MethodDeprecation $methodDeprecation)
    {
        if (!isset($this->methodDeprecations[$methodDeprecation->parentName()])) {
            $this->methodDeprecations[$methodDeprecation->parentName()] = array();
        }

        $this->methodDeprecations[$methodDeprecation->parentName()][$methodDeprecation->name()] = $methodDeprecation;
    }

    /**
     * @return MethodDeprecation[]
     */
    public function methodDeprecations()
    {
        return $this->methodDeprecations;
    }

    /**
     * @return bool
     */
    public function hasMethodDeprecations()
    {
        return count($this->methodDeprecations()) > 0;
    }

    /**
     * @param string $methodName
     * @param string $className
     *
     * @return bool
     */
    public function hasMethodDeprecation($methodName, $className)
    {
        return isset($this->methodDeprecations[$className][$methodName]);
    }

    /**
     * @param string $methodName
     * @param string $className
     *
     * @return MethodDeprecation
     */
    public function getMethodDeprecation($methodName, $className)
    {
        return $this->hasMethodDeprecation($methodName, $className)
            ? $this->methodDeprecations[$className][$methodName]
            : null;
    }

    /**
     * @param DeprecatedLanguageUsage $deprecatedLanguageUsage
     */
    public function addDeprecatedLanguageUsage(DeprecatedLanguageUsage $deprecatedLanguageUsage)
    {
        $this->deprecatedLanguageUsages[] = $deprecatedLanguageUsage;
    }

    /**
     * @return DeprecatedLanguageUsage[]
     */
    public function getDeprecatedLanguageUsages()
    {
        return $this->deprecatedLanguageUsages;
    }

    /**
     * @return bool
     */
    public function hasDeprecations()
    {
        return $this->hasClassDeprecations() || $this->hasInterfaceDeprecations() || $this->hasMethodDeprecations();
    }
}
