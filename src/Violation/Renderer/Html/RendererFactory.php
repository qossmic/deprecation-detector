<?php
namespace SensioLabs\DeprecationDetector\Violation\Renderer\Html;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RendererFactory
 *
 * This class will assists in creating new instance of HTML renderer.
 *
 * @author Karl Spies <karl.spies@gmx.net>
 */
class RendererFactory
{
    private $messageHelper;
    private $filesystem;

    public function __construct(MessageHelper $messageHelper, Filesystem $filesystem)
    {
        $this->messageHelper = $messageHelper;
        $this->filesystem = $filesystem;
    }

    public function createHtmlOutputRenderer($outputFile)
    {
        return new Renderer($this->messageHelper, $this->filesystem, $outputFile);
    }
}
