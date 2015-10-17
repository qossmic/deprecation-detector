<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Deprecation;

interface DeprecationInterface
{
    /**
     * Returns the name of the deprecation.
     *
     * @return string
     */
    public function name();

    /**
     * Returns the comment of the deprecated annotation.
     *
     * @return int
     */
    public function comment();
}
