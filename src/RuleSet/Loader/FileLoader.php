<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileLoader.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class FileLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadRuleSet($path)
    {
        if (!is_file($path)) {
            throw new CouldNotLoadRuleSetException(sprintf(
                'Ruleset "%s" does not exist, aborting.',
                $path
                )
            );
        }

        $file = new SplFileInfo($path, null, null);
        $ruleSet = unserialize($file->getContents());

        if (!$ruleSet instanceof RuleSet) {
            throw new CouldNotLoadRuleSetException(sprintf(
                'Ruleset "%s" is invalid, aborting.',
                $path
                )
            );
        }

        return $ruleSet;
    }
}
