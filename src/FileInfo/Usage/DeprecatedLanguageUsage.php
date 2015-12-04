<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\DeprecationInterface;

class DeprecatedLanguageUsage implements UsageInterface, DeprecationInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var int
     */
    private $line;

    /**
     * @param string $name
     * @param string $comment
     * @param int    $line
     */
    public function __construct($name, $comment, $line)
    {
        $this->name = $name;
        $this->comment = $comment;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function comment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->line;
    }
}
