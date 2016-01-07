<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Deprecation;

class FunctionDeprecation implements DeprecationInterface
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
     * @param string $name
     * @param string $comment
     */
    public function __construct($name, $comment)
    {
        $this->name = $name;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function comment()
    {
        return $this->comment;
    }
}
