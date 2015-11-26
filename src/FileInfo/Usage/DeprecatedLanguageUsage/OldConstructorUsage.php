<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;

class OldConstructorUsage implements DeprecatedLanguageUsageInterface
{
    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @param int $lineNumber
     */
    public function __construct($lineNumber)
    {
        $this->lineNumber = $lineNumber;
    }

    public function comment()
    {
        return 'Since PHP 7.0, use __construct() instead.';
    }

    public function name()
    {
        return 'DeprecatedConstructor';
    }

    public function getLineNumber()
    {
        return $this->lineNumber;
    }
}
