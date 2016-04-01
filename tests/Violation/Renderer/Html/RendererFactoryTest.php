<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\Html;

use PHPUnit_Framework_TestCase;
use SensioLabs\DeprecationDetector\Violation\Renderer\Html\Renderer;
use SensioLabs\DeprecationDetector\Violation\Renderer\Html\RendererFactory;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RendererFactoryTest.
 *
 * @author Karl Spies <karl.spies@gmx.net>
 */
class RendererFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateRenderer()
    {
        $factory = new RendererFactory(new MessageHelper(), new Filesystem());

        $this->assertInstanceOf(
            Renderer::class,
            $factory->createHtmlOutputRenderer('./output.html')
        );
    }
}
