<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

class ClassUsage implements UsageInterface
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @param string $className
     * @param int    $lineNumber
     */
    public function __construct($className, $lineNumber)
    {
        $this->className = $className;
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return string
     */
    public function name()
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
