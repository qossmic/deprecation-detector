<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Output;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use Symfony\Component\Console\Helper\ProgressBar;

class ProgressOutput
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var bool
     */
    private $verbose;

    /**
     * @param ProgressBar $progressBar
     * @param bool        $verbose
     * @param string      $label
     */
    public function __construct(
        ProgressBar $progressBar,
        $verbose,
        $label)
    {
        $this->progressBar = $progressBar;
        $this->verbose = $verbose;
        $this->label = $label;
    }

    public function start($total)
    {
        if (!$this->verbose) {
            return;
        }

        $format = $this->label.': <comment>loading %max% files into memory, this can take some time</comment>';
        $this->progressBar->setFormat($format);
        $this->progressBar->start($total);
    }

    public function advance($current, PhpFileInfo $file = null)
    {
        if (!$this->verbose) {
            return;
        }

        if (1 === $current) {
            $format = '<info>%message%</info>'."\n".$this->label.': <info>%current%</info>/<info>%max%</info>';
            $this->progressBar->clear();
            $this->progressBar->setFormat($format);
        }

        if ($file instanceof PhpFileInfo) {
            $message = $file->getRelativePathname();
            $this->progressBar->setMessage($message);
        }

        $this->progressBar->clear();
        $this->progressBar->advance();
        $this->progressBar->display();
    }

    public function end()
    {
        if (!$this->verbose) {
            return;
        }

        $this->progressBar->clear();
        $this->progressBar->finish();
    }
}
