<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

class FunctionUsage implements UsageInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $line;

    /**
     * @param string $name
     * @param int $line
     */
    public function __construct($name, $line)
    {
        $this->name = $name;
        $this->line = $line;
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
