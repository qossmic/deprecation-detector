<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage;

interface UsageInterface
{
    /**
     * Returns the name of the usage.
     *
     * @return string
     */
    public function name();

    /**
     * Returns the line number of usage.
     *
     * @return int
     */
    public function getLineNumber();
}
