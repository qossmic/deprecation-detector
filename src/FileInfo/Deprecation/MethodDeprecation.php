<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Deprecation;

class MethodDeprecation implements DeprecationInterface
{
    /**
     * @var string
     */
    private $parentName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $comment;

    /**
     * @param string $parentName
     * @param string $name
     * @param string $comment
     */
    public function __construct($parentName, $name, $comment)
    {
        $this->parentName = $parentName;
        $this->name = $name;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function parentName()
    {
        return $this->parentName;
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
}
