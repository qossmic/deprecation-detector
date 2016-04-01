<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter;

class ShortPathFormatter implements PathFormatterInterface
{
    /**
     * @var string
     */
    private $omittedPrefix;

    /**
     * @param string $omittedPrefix
     */
    public function __construct($omittedPrefix)
    {
        $this->omittedPrefix = $omittedPrefix;
    }

    public function format($path)
    {
        if (substr($path, 0, strlen($this->omittedPrefix)) == $this->omittedPrefix) {
            return substr($path, strlen($this->omittedPrefix));
        }

        return $path;
    }
}
