<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

class SuperTypeUsage implements UsageInterface
{
    /**
     * @var string
     */
    protected $superTypeName;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @param string $superTypeName
     * @param string $className
     * @param int    $lineNumber
     */
    public function __construct($superTypeName, $className, $lineNumber)
    {
        $this->superTypeName = $superTypeName;
        $this->className = $className;
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->superTypeName;
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
