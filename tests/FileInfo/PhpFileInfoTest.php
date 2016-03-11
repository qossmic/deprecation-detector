<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\FunctionDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\MethodDefinition;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage;
use Symfony\Component\Finder\SplFileInfo;

class PhpFileInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());

        $this->assertInstanceOf(PhpFileInfo::class, $fileInfo);
    }

    public function testAddAndGetClassUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->classUsages());

        $classUsage = $this->prophesize(ClassUsage::class)->reveal();
        $fileInfo->addClassUsage($classUsage);
        $this->assertSame(array($classUsage), $fileInfo->classUsages());
    }

    public function testAddAndGetInterfaceUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->interfaceUsages());

        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $interfaceUsage->className()->willReturn('className');
        $interfaceUsage = $interfaceUsage->reveal();

        $fileInfo->addInterfaceUsage($interfaceUsage);
        $this->assertSame(array('className' => array($interfaceUsage)), $fileInfo->interfaceUsages());
    }

    public function testAddAndGetSuperTypeUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->superTypeUsages());

        $superTypeUsage = $this->prophesize(SuperTypeUsage::class);
        $superTypeUsage->className()->willReturn('className');
        $superTypeUsage = $superTypeUsage->reveal();

        $fileInfo->addSuperTypeUsage($superTypeUsage);
        $this->assertSame(array('className' => $superTypeUsage), $fileInfo->superTypeUsages());
    }

    public function testAddAndGetMethodUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->methodUsages());

        $methodUsage = $this->prophesize(MethodUsage::class)->reveal();
        $fileInfo->addMethodUsage($methodUsage);
        $this->assertSame(array($methodUsage), $fileInfo->methodUsages());
    }

    public function testAddAndGetTypeHintUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->methodUsages());

        $typeHintUsage = $this->prophesize(TypeHintUsage::class)->reveal();
        $fileInfo->addTypeHintUsage($typeHintUsage);
        $this->assertSame(array($typeHintUsage), $fileInfo->typeHintUsages());
    }

    public function testClassDeprecations()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->classDeprecations());

        $classDeprecation = $this->prophesize(ClassDeprecation::class);
        $classDeprecation->name()->willReturn('className');
        $classDeprecation = $classDeprecation->reveal();

        $this->assertFalse($fileInfo->hasDeprecations());
        $this->assertFalse($fileInfo->hasClassDeprecations());
        $this->assertFalse($fileInfo->hasClassDeprecation('className'));
        $this->assertNull($fileInfo->getClassDeprecation('className'));

        $fileInfo->addClassDeprecation($classDeprecation);
        $this->assertSame(array('className' => $classDeprecation), $fileInfo->classDeprecations());

        $this->assertTrue($fileInfo->hasDeprecations());
        $this->assertTrue($fileInfo->hasClassDeprecations());
        $this->assertTrue($fileInfo->hasClassDeprecation('className'));
        $this->assertSame($classDeprecation, $fileInfo->getClassDeprecation('className'));
    }

    public function testInterfaceDeprecations()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->interfaceDeprecations());

        $interfaceDeprecation = $this
            ->prophesize(InterfaceDeprecation::class);
        $interfaceDeprecation->name()->willReturn('interfaceName');
        $interfaceDeprecation = $interfaceDeprecation->reveal();

        $this->assertFalse($fileInfo->hasDeprecations());
        $this->assertFalse($fileInfo->hasInterfaceDeprecations());
        $this->assertFalse($fileInfo->hasInterfaceDeprecation('interfaceName'));
        $this->assertNull($fileInfo->getInterfaceDeprecation('interfaceName'));

        $fileInfo->addInterfaceDeprecation($interfaceDeprecation);
        $this->assertSame(array('interfaceName' => $interfaceDeprecation), $fileInfo->interfaceDeprecations());

        $this->assertTrue($fileInfo->hasDeprecations());
        $this->assertTrue($fileInfo->hasInterfaceDeprecations());
        $this->assertTrue($fileInfo->hasInterfaceDeprecation('interfaceName'));
        $this->assertSame($interfaceDeprecation, $fileInfo->getInterfaceDeprecation('interfaceName'));
    }

    public function testMethodDeprecations()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->methodDeprecations());

        $methodDeprecation = $this->prophesize(MethodDeprecation::class);
        $methodDeprecation->parentName()->willReturn('className');
        $methodDeprecation->name()->willReturn('methodName');
        $methodDeprecation = $methodDeprecation->reveal();

        $this->assertFalse($fileInfo->hasDeprecations());
        $this->assertFalse($fileInfo->hasMethodDeprecations());
        $this->assertFalse($fileInfo->hasMethodDeprecation('methodName', 'className'));
        $this->assertNull($fileInfo->getMethodDeprecation('methodName', 'className'));

        $fileInfo->addMethodDeprecation($methodDeprecation);
        $this->assertSame(
            array('className' => array('methodName' => $methodDeprecation)),
            $fileInfo->methodDeprecations()
        );

        $this->assertTrue($fileInfo->hasDeprecations());
        $this->assertTrue($fileInfo->hasMethodDeprecations());
        $this->assertTrue($fileInfo->hasMethodDeprecation('methodName', 'className'));
        $this->assertSame($methodDeprecation, $fileInfo->getMethodDeprecation('methodName', 'className'));
    }

    public function testHasAndGetInterfaceUsageByClass()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertFalse($fileInfo->hasInterfaceUsageByClass('someClass'));
        $this->assertSame(array(), $fileInfo->getInterfaceUsageByClass('someClass'));

        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $interfaceUsage->className()->willReturn('someClass');
        $interfaceUsage = $interfaceUsage->reveal();

        $fileInfo->addInterfaceUsage($interfaceUsage);
        $this->assertTrue($fileInfo->hasInterfaceUsageByClass('someClass'));
        $this->assertSame(array($interfaceUsage), $fileInfo->getInterfaceUsageByClass('someClass'));
    }

    public function testHasAndGetSuperTypeUsageByClass()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertFalse($fileInfo->hasSuperTypeUsageByClass('someClass'));
        $this->assertNull($fileInfo->getSuperTypeUsageByClass('someClass'));

        $superTypeUsage = $this->prophesize(SuperTypeUsage::class);
        $superTypeUsage->className()->willReturn('someClass');
        $superTypeUsage = $superTypeUsage->reveal();

        $fileInfo->addSuperTypeUsage($superTypeUsage);
        $this->assertTrue($fileInfo->hasSuperTypeUsageByClass('someClass'));
        $this->assertSame($superTypeUsage, $fileInfo->getSuperTypeUsageByClass('someClass'));
    }

    public function testAddAndGetMethodDefinitions()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->methodDefinitions());

        $methodDefinition = $this->prophesize(MethodDefinition::class)->reveal();

        $fileInfo->addMethodDefinition($methodDefinition);

        $this->assertSame(array($methodDefinition), $fileInfo->methodDefinitions());
    }

    public function testFunctionDeprecation()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->functionDeprecations());

        $functionDeprecation = $this->prophesize(FunctionDeprecation::class);
        $functionDeprecation->name()->willReturn('functionName');
        $functionDeprecation = $functionDeprecation->reveal();

        $this->assertFalse($fileInfo->hasDeprecations());
        $this->assertFalse($fileInfo->hasFunctionDeprecations());

        $fileInfo->addFunctionDeprecation($functionDeprecation);
        $this->assertSame(array('functionName' => $functionDeprecation), $fileInfo->functionDeprecations());

        $this->assertTrue($fileInfo->hasDeprecations());
        $this->assertTrue($fileInfo->hasFunctionDeprecations());
    }

    public function testAddAndGetFunctionUsage()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->getFunctionUsages());

        $functionUsage = $this->prophesize(FunctionUsage::class)->reveal();

        $fileInfo->addFunctionUsage($functionUsage);

        $this->assertSame(array($functionUsage), $fileInfo->getFunctionUsages());
    }

    public function testAddAndGetDeprecatedLanguageUsage()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize(SplFileInfo::class)->reveal());
        $this->assertSame(array(), $fileInfo->getDeprecatedLanguageUsages());

        $deprecatedLanguageUsage = $this->prophesize(DeprecatedLanguageUsage::class)->reveal();

        $fileInfo->addDeprecatedLanguageUsage($deprecatedLanguageUsage);

        $this->assertSame(array($deprecatedLanguageUsage), $fileInfo->getDeprecatedLanguageUsages());
    }
}
