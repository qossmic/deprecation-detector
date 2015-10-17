<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Violation\Violation;

interface ViolationCheckerInterface
{
    /**
     * @param PhpFileInfo $phpFileInfo
     *
     * @return Violation[]
     */
    public function check(PhpFileInfo $phpFileInfo);
}
