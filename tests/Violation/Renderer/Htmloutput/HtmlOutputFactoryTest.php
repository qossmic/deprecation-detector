<?php
namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\HtmlOutput;

use PHPUnit_Framework_TestCase;
use SensioLabs\DeprecationDetector\Violation\Renderer\HtmlOutput\HtmlOutputRendererFactory;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use Symfony\Component\Filesystem\Filesystem;

class HtmlOutputFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateRenderer()
    {
        $factory = new HtmlOutputRendererFactory(new MessageHelper(), new Filesystem());

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\HtmlOutput\HtmlOutputRenderer',
            $factory->createHtmlOutputRenderer('./output.html')
        );
    }
}
