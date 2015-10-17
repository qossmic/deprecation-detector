<?php

namespace SensioLabs\DeprecationDetector\FileInfo;

use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class MethodDefinition implements UsageInterface
{
    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var string
     */
    protected $parentName;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @param string $methodName
     * @param string $parentName
     * @param int    $lineNumber
     */
    public function __construct($methodName, $parentName, $lineNumber)
    {
        $this->methodName = $methodName;
        $this->parentName = $parentName;
        $this->lineNumber = $lineNumber;
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
    public function parentName()
    {
        return $this->parentName;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }
}
