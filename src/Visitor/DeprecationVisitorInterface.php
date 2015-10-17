<?php

namespace SensioLabs\DeprecationDetector\Visitor;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;

interface DeprecationVisitorInterface extends VisitorInterface
{
    /**
     * @param PhpFileInfo $phpFileInfo
     *
     * @return $this
     */
    public function setPhpFileInfo(PhpFileInfo $phpFileInfo);
}
