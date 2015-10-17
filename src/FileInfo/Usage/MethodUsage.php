<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

class MethodUsage implements UsageInterface
{
    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @var bool
     */
    protected $isStatic;

    /**
     * @param string $methodName
     * @param string $className
     * @param int    $lineNumber
     * @param bool   $isStatic
     */
    public function __construct($methodName, $className, $lineNumber, $isStatic)
    {
        $this->methodName = $methodName;
        $this->className = $className;
        $this->lineNumber = $lineNumber;
        $this->isStatic = $isStatic;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function className()
    {
        return $this->className;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return $this->isStatic;
    }
}
