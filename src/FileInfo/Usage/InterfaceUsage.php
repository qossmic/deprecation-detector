<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

class InterfaceUsage implements UsageInterface
{
    /**
     * @var string
     */
    protected $interfaceName;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @param string $interfaceName
     * @param string $className
     * @param int    $lineNumber
     */
    public function __construct($interfaceName, $className, $lineNumber)
    {
        $this->interfaceName = $interfaceName;
        $this->className = $className;
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->interfaceName;
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
}
