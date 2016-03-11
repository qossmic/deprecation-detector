<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use org\bovigo\vfs\vfsStream;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileDoesNotExistsException;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileIsInvalidException;

class ComposerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $factory = new ComposerFactory();

        $this->assertInstanceOf(ComposerFactory::class, $factory);
    }

    public function testFromLockThrowsComposerFileDoesNotExistException()
    {
        $factory = new ComposerFactory();

        $this->setExpectedException(
            ComposerFileDoesNotExistsException::class,
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
            ComposerFileIsInvalidException::class,
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
            '{"_readme": [],"hash": "ac82fae1f7095370dc4c7299aa637a30","content-hash": "3faf23edaf51060bfe1830506104ec83","packages": [{"name": "vendor/lib","version": "v2.7.6"}], "packages-dev": [{"name": "vendor/devlib","version": "v0.0.6"}]}'
        );
        $root->addChild($file);
        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Composer',
            $composer = $factory->fromLock(vfsStream::url('root/composer.lock'))
        );
    }
}
