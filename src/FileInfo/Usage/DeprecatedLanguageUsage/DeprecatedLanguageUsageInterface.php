<?php

namespace SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

interface DeprecatedLanguageUsageInterface extends UsageInterface
{
    /**
     * @return string
     */
    public function comment();
}
