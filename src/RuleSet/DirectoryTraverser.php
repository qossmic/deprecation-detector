<?php

namespace SensioLabs\DeprecationDetector\RuleSet;

use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;

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
     * @param string  $path
     * @param RuleSet $ruleSet
     *
     * @return RuleSet
     */
    public function traverse($path, RuleSet $ruleSet = null)
    {
        $result = $this->finder->parsePhpFiles($path);

        if (!$ruleSet instanceof RuleSet) {
            $ruleSet = new RuleSet();
        }

        foreach ($result->parsedFiles() as $file) {
            if ($file->hasDeprecations()) {
                $ruleSet->merge($file);
            }
        }

        return $ruleSet;
    }
}
