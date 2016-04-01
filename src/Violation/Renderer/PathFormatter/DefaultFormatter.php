<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter;

class DefaultFormatter implements PathFormatterInterface
{
    public function format($path)
    {
        return $path;
    }
}
