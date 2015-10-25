<?php
namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\HtmlOutput;

use PHPUnit_Framework_TestCase;
use SensioLabs\DeprecationDetector\Violation\Renderer\Html\HtmlRendererFactory;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use Symfony\Component\Filesystem\Filesystem;

class HtmlOutputFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateRenderer()
    {
        $factory = new HtmlRendererFactory(new MessageHelper(), new Filesystem());

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\Html\HtmlRenderer',
            $factory->createHtmlOutputRenderer('./output.html')
        );
    }
}
