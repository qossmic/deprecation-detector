<?php

namespace SensioLabs\DeprecationDetector\Violation;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class Violation
{
    /**
     * @var PhpFileInfo
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var UsageInterface
     */
    private $usage;

    /**
     * @param UsageInterface $usage
     * @param PhpFileInfo    $file
     * @param $comment
     */
    public function __construct(UsageInterface $usage, PhpFileInfo $file, $comment)
    {
        $this->file = $file;
        $this->line = $usage->getLineNumber();
        $this->usage = $usage;
        $this->comment = $comment;
    }

    /**
     * @return PhpFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return UsageInterface
     */
    public function getUsage()
    {
        return $this->usage;
    }
}
