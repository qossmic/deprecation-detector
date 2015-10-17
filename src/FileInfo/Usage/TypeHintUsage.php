<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

class TypeHintUsage implements UsageInterface
{
    /**
     * @var string
     */
    protected $typeHintName;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @param string $typeHintName
     * @param int    $lineNumber
     */
    public function __construct($typeHintName, $lineNumber)
    {
        $this->typeHintName = $typeHintName;
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->typeHintName;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }
}
