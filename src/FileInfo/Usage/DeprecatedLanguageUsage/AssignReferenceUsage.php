<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;

class AssignReferenceUsage implements DeprecatedLanguageUsageInterface
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

    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return 'AssignReference';
    }

    /**
     * {@inheritDoc}
     */
    public function comment()
    {
        return 'Since PHP 5.3 use normal assignment instead.';
    }

    /**
     * {@inheritDoc}
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }
}
