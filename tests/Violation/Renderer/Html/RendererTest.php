<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\Html;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit_Framework_TestCase;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Violation\Renderer\Html\Renderer;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Violation;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RendererTest.
 *
 * @author Karl Spies <karl.spies@gmx.net>
 */
class RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * set up test environment.
     */
    public function setUp()
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    public function testRenderViolations()
    {
        $fileSystem = $this->prophesize(Filesystem::class);
        $fileSystem->mkdir('exampleDir');

        $fileInfo = $this->prophesize(PhpFileInfo::class);
        $fileInfo->getPathname()->willReturn('just/a/path');

        $violation = $this->prophesize(Violation::class);
        $violation->getFile()->willReturn($fileInfo->reveal());
        $violation->getLine()->willReturn('12');
        $violation->getComment()->willReturn('Just a comment');

        $messageHelper = $this->prophesize(MessageHelper::class);
        $messageHelper->getViolationMessage($violation->reveal())->willReturn('testMethod');

        $renderer = new Renderer(
            $messageHelper->reveal(),
            $fileSystem->reveal(),
            vfsStream::url('exampleDir/output.html')
        );

        $renderer->renderViolations([$violation->reveal()], []);

        $this->assertFileExists(vfsStream::url('exampleDir/output.html'));

        $fileContent = file_get_contents(vfsStream::url('exampleDir/output.html'));

        $this->assertContains('testMethod', $fileContent);
        $this->assertContains('12', $fileContent);
        $this->assertContains('Just a comment', $fileContent);
    }
}
