<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use org\bovigo\vfs\vfsStream;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory;

class ComposerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $factory = new ComposerFactory();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory', $factory);
    }

    public function testFromLockThrowsComposerFileDoesNotExistException()
    {
        $factory = new ComposerFactory();

        $this->setExpectedException(
            'SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileDoesNotExistsException',
            'composer.lock file "path/to/not/existing/composer.lock" does not exist.'
        );
        $factory->fromLock('path/to/not/existing/composer.lock');
    }

    public function testFromLockThrowsComposerFileIsInvalidException()
    {
        $factory = new ComposerFactory();

        $root = vfsStream::setup('root');
        $file = vfsStream::newFile('composer.lock');
        $file->setContent('invalid;lock_file;');
        $root->addChild($file);

        $this->setExpectedException(
            'SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileIsInvalidException',
            'composer.lock file "vfs://root/composer.lock" is invalid.'
        );
        $factory->fromLock(vfsStream::url('root/composer.lock'));
    }

    public function testFromLockReturnsComposerObject()
    {
        $factory = new ComposerFactory();

        $root = vfsStream::setup('root');
        $file = vfsStream::newFile('composer.lock');
        $file->setContent(
            '{"_readme": [],"hash": "ac82fae1f7095370dc4c7299aa637a30","content-hash": "3faf23edaf51060bfe1830506104ec83","packages": [], "packages-dev": []}'
        );
        $root->addChild($file);
        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Composer',
            $composer = $factory->fromLock(vfsStream::url('root/composer.lock'))
        );
    }
}
