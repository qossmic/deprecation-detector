<?php

namespace SensioLabs\DeprecationDetector\RuleSet;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\Parser\DeprecationParser;

/**
 * Class Traverser.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class DirectoryTraverser
{
    /**
     * @var ParsedPhpFileFinder
     */
    private $finder;

    /**
     * @param ParsedPhpFileFinder $finder
     */
    public function __construct(ParsedPhpFileFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param string $path
     * @param RuleSet $ruleSet
     *
     * @return RuleSet
     */
    public function traverse($path, RuleSet $ruleSet = null)
    {
        $files = $this->finder->in($path);

        if (!$ruleSet instanceof RuleSet) {
            $ruleSet = new RuleSet();
        }

        foreach ($files as $i => $file) {
            /** @var PhpFileInfo $file */
            if ($file->hasDeprecations()) {
                $ruleSet->merge($file);
            }
        }

        return $ruleSet;
    }
}
