<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing;

use Composer\Autoload\ClassLoader;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;
use SensioLabs\DeprecationDetector\Parser\UsageParser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AncestorResolver
{
    /**
     * @var array
     */
    protected $definitionFiles;

    /**
     * @var array
     */
    protected $sourcePaths;

    /**
     * @var ClassLoader
     */
    protected $composerLoader;

    /**
     * @var UsageParser
     */
    protected $usageParser;

    /**
     * @param UsageParser $usageParser
     */
    public function __construct(UsageParser $usageParser)
    {
        $this->definitionFiles = array();
        $this->sourcePaths = array();
        $this->usageParser = $usageParser;
    }

    /**
     * @param string|array $sourcePaths
     */
    public function setSourcePaths($sourcePaths)
    {
        foreach ((array) $sourcePaths as $path) {
            if (is_dir($path) && !in_array($path, $this->sourcePaths)) {
                $this->sourcePaths[] = $path;
            }
        }
    }

    /**
     * @param PhpFileInfo $phpFileInfo
     * @param $name
     *
     * @return UsageInterface[]
     */
    public function getClassAncestors(PhpFileInfo $phpFileInfo, $name)
    {
        $ancestors = array();

        $interfaces = $phpFileInfo->getInterfaceUsageByClass($name);
        foreach ($interfaces as $interface) {
            $ancestors = array_merge(
                $ancestors,
                $this->resolveInterfaceAncestors($interface->name())
            );
        }

        $superType = $phpFileInfo->getSuperTypeUsageByClass($name);
        if (null !== $superType) {
            $ancestors = array_merge(
                $ancestors,
                $this->resolveClassAncestors($superType->name())
            );
        }

        return $ancestors;
    }

    /**
     * @param $interfaceName
     *
     * @return array
     */
    protected function resolveInterfaceAncestors($interfaceName)
    {
        $ancestors = array($interfaceName);
        $phpFileInfo = $this->getDefinitionFile('interface', $interfaceName);

        if (null !== $phpFileInfo) {
            if ($phpFileInfo->hasInterfaceUsageByClass($interfaceName)) {
                $interfaceUsages = $phpFileInfo->getInterfaceUsageByClass($interfaceName);
                foreach ($interfaceUsages as $interfaceUsage) {
                    $ancestors = array_merge($ancestors, $this->resolveInterfaceAncestors($interfaceUsage->name()));
                }
            }
        }

        return $ancestors;
    }

    /**
     * @param $className
     *
     * @return array
     */
    protected function resolveClassAncestors($className)
    {
        $ancestors = array($className);
        $phpFileInfo = $this->getDefinitionFile('class', $className);

        if (null !== $phpFileInfo) {
            if ($phpFileInfo->hasInterfaceUsageByClass($className)) {
                $interfaceUsages = $phpFileInfo->getInterfaceUsageByClass($className);
                foreach ($interfaceUsages as $interfaceUsage) {
                    $ancestors = array_merge($ancestors, $this->resolveInterfaceAncestors($interfaceUsage->name()));
                }
            }

            if ($phpFileInfo->hasSuperTypeUsageByClass($className)) {
                $superTypeUsage = $phpFileInfo->getSuperTypeUsageByClass($className);
                $ancestors = array_merge($ancestors, $this->resolveClassAncestors($superTypeUsage->name()));
            }
        }

        return $ancestors;
    }

    /**
     * @param $type
     * @param $name
     *
     * @return PhpFileInfo|null
     */
    protected function getDefinitionFile($type, $name)
    {
        if (array_key_exists($name, $this->definitionFiles)) {
            return $this->definitionFiles[$name];
        }

        $this->definitionFiles[$name] = null;
        $this->definitionFiles[$name] = $this->findDefinitionFileByComposer($name);

        if (null !== $this->definitionFiles[$name]) {
            return $this->definitionFiles[$name];
        }

        $this->definitionFiles[$name] = $this->findDefinitionFileByName($name);

        if (null !== $this->definitionFiles[$name]) {
            return $this->definitionFiles[$name];
        }

        $this->definitionFiles[$name] = $this->findDefinitionFileByRegex($type, $name);

        return $this->definitionFiles[$name];
    }

    /**
     * @param $name
     *
     * @return PhpFileInfo|null
     */
    protected function findDefinitionFileByComposer($name)
    {
        if (null === $this->composerLoader) {
            $this->initComposerLoader();
        }

        if (false === $this->composerLoader) {
            return;
        }

        $filePath = $this->composerLoader->findFile($name);

        if (empty($filePath)) {
            return;
        }

        $file = new PhpFileInfo($filePath, null, null);

        return $this->usageParser->parseFile($file);
    }

    protected function initComposerLoader()
    {
        $finder = new Finder();
        $finder
            ->name('autoload.php')
            ->in($this->sourcePaths)
            ->contains('@generated by Composer')
        ;

        $files = iterator_to_array($finder);
        if (count($files) == 0) {
            $this->composerLoader = false;

            return;
        }

        /** @var PhpFileInfo $file */
        $file = current($files);
        $this->composerLoader = include $file->getPathname();
    }

    /**
     * @param $name
     *
     * @return PhpFileInfo|null
     */
    protected function findDefinitionFileByName($name)
    {
        $namespaceParts = explode('\\', $name);
        $filename = array_pop($namespaceParts).'.php';
        $namespace = implode('\\', $namespaceParts);

        $finder = new Finder();
        $finder
            ->name($filename)
            ->in($this->sourcePaths)
        ;

        $files = array();
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            if (empty($namespace) || is_int(strpos($file->getContents(), $namespace))) {
                $baseFile = PhpFileInfo::create($file);
                $files[] = $this->usageParser->parseFile($baseFile);
            }
        }

        $file = current($files);

        if (!$file instanceof PhpFileInfo) {
            return;
        }

        return $file;
    }

    /**
     * @param $type
     * @param $name
     *
     * @return PhpFileInfo|null
     */
    protected function findDefinitionFileByRegex($type, $name)
    {
        $namespaceParts = explode('\\', $name);

        $definition = sprintf('%s %s[;\{\s]', $type, array_pop($namespaceParts));
        if (count($namespaceParts) > 0) {
            $namespace = sprintf('namespace %s', implode('\\\\', $namespaceParts));
        } else {
            $namespace = '';
        }

        $files = new Finder();
        $files
            ->name('*.php')
            ->contains(sprintf('/%s.*%s/s', $namespace, $definition))
            ->in($this->sourcePaths)
        ;

        if (!$namespace) {
            $files->notContains('/namespace\s[^;]+/');
        }

        $file = current(iterator_to_array($files));

        if (!$file instanceof SplFileInfo) {
            return;
        }

        $baseFile = PhpFileInfo::create($file);

        return $this->usageParser->parseFile($baseFile);
    }
}
