<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;

class PhpFileInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo', $fileInfo);
    }

    public function testAddAndGetClassUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->classUsages());

        $classUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage')->reveal();
        $fileInfo->addClassUsage($classUsage);
        $this->assertSame(array($classUsage), $fileInfo->classUsages());
    }

    public function testAddAndGetInterfaceUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->interfaceUsages());

        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $interfaceUsage->className()->willReturn('className');
        $interfaceUsage = $interfaceUsage->reveal();

        $fileInfo->addInterfaceUsage($interfaceUsage);
        $this->assertSame(array('className' => array($interfaceUsage)), $fileInfo->interfaceUsages());
    }

    public function testAddAndGetSuperTypeUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->superTypeUsages());

        $superTypeUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');
        $superTypeUsage->className()->willReturn('className');
        $superTypeUsage = $superTypeUsage->reveal();

        $fileInfo->addSuperTypeUsage($superTypeUsage);
        $this->assertSame(array('className' => $superTypeUsage), $fileInfo->superTypeUsages());
    }

    public function testAddAndGetMethodUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->methodUsages());

        $methodUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage')->reveal();
        $fileInfo->addMethodUsage($methodUsage);
        $this->assertSame(array($methodUsage), $fileInfo->methodUsages());
    }

    public function testAddAndGetTypeHintUsages()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->methodUsages());

        $typeHintUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage')->reveal();
        $fileInfo->addTypeHintUsage($typeHintUsage);
        $this->assertSame(array($typeHintUsage), $fileInfo->typeHintUsages());
    }

    public function testClassDeprecations()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->classDeprecations());

        $classDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation');
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
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->interfaceDeprecations());

        $interfaceDeprecation = $this
            ->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation');
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
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->methodDeprecations());

        $methodDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation');
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
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertFalse($fileInfo->hasInterfaceUsageByClass('someClass'));
        $this->assertSame(array(), $fileInfo->getInterfaceUsageByClass('someClass'));

        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $interfaceUsage->className()->willReturn('someClass');
        $interfaceUsage = $interfaceUsage->reveal();

        $fileInfo->addInterfaceUsage($interfaceUsage);
        $this->assertTrue($fileInfo->hasInterfaceUsageByClass('someClass'));
        $this->assertSame(array($interfaceUsage), $fileInfo->getInterfaceUsageByClass('someClass'));
    }

    public function testHasAndGetSuperTypeUsageByClass()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertFalse($fileInfo->hasSuperTypeUsageByClass('someClass'));
        $this->assertSame(null, $fileInfo->getSuperTypeUsageByClass('someClass'));

        $superTypeUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');
        $superTypeUsage->className()->willReturn('someClass');
        $superTypeUsage = $superTypeUsage->reveal();

        $fileInfo->addSuperTypeUsage($superTypeUsage);
        $this->assertTrue($fileInfo->hasSuperTypeUsageByClass('someClass'));
        $this->assertSame($superTypeUsage, $fileInfo->getSuperTypeUsageByClass('someClass'));
    }

    public function testAddAndGetMethodDefinitions()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->methodDefinitions());

        $methodDefinition = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\MethodDefinition')->reveal();

        $fileInfo->addMethodDefinition($methodDefinition);

        $this->assertSame(array($methodDefinition), $fileInfo->methodDefinitions());
    }

    public function testFunctionDeprecation()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->functionDeprecations());

        $functionDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\FunctionDeprecation');
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
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->getFunctionUsages());

        $functionUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage')->reveal();

        $fileInfo->addFunctionUsage($functionUsage);

        $this->assertSame(array($functionUsage), $fileInfo->getFunctionUsages());
    }

    public function testAddAndGetDeprecatedLanguageUsage()
    {
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $this->assertSame(array(), $fileInfo->getDeprecatedLanguageUsages());

        $deprecatedLanguageUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage')->reveal();

        $fileInfo->addDeprecatedLanguageUsage($deprecatedLanguageUsage);

        $this->assertSame(array($deprecatedLanguageUsage), $fileInfo->getDeprecatedLanguageUsages());
    }
}
