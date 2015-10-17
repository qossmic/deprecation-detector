<?php

namespace SensioLabs\DeprecationDetector\Parser;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;

interface ParserInterface
{
    /**
     * @param PhpFileInfo $phpFileInfo
     *
     * @return PhpFileInfo
     */
    public function parseFile(PhpFileInfo $phpFileInfo);
}
