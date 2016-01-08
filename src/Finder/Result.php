<?php

namespace SensioLabs\DeprecationDetector\Finder;

use PhpParser\Error;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;

class Result
{
    private $files;

    private $errors;

    private $fileCount;

    /**
     * @param PhpFileInfo[] $files
     * @param Error[] $errors
     * @param int $fileCount
     */
    public function __construct(array $files, array $errors, $fileCount)
    {
        $this->files = $files;
        $this->errors = $errors;
        $this->fileCount = $fileCount;
    }

    public function parsedFiles()
    {
        return $this->files;
    }

    public function parserErrors()
    {
        return $this->errors;
    }

    public function fileCount()
    {
        return $this->fileCount;
    }
}
