<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit_Framework_TestCase;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\HtmlOutputRenderer;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Violation;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;

class HtmlOutputRendererTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    public function testRenderViolations()
    {
        $renderer = new HtmlOutputRenderer(
            new NullOutput(),
            new MessageHelper(),
            new Filesystem(),
            vfsStream::url('exampleDir/output.html')
        );

        $violations[] = new Violation(
            new ClassUsage('TesterClass', 33),
            new PhpFileInfo('file1.php', '..', '../'),
            'This is just a test'
        );
        $violations[] = new Violation(
            new MethodUsage('testMethod', 'TestClass', 44, false),
            new PhpFileInfo('file2.php', '..', '../'),
            'This is just a test'
        );
        $violations[] = new Violation(
            new InterfaceUsage('HelloInterface', 'HelloClass', 12),
            new PhpFileInfo('file2.php', '..', '../'),
            'This is just a test'
        );

        $renderer->renderViolations($violations);

        $this->assertFileExists(vfsStream::url('exampleDir/output.html'));

        $fileOutPut = file_get_contents(vfsStream::url('exampleDir/output.html'));

        $this->assertContains('testMethod', $fileOutPut);
        $this->assertContains('HelloInterface', $fileOutPut);
        $this->assertContains('TesterClass', $fileOutPut);

    }

}
