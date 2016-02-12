<?php

namespace SensioLabs\DeprecationDetector\Finder;

use Symfony\Component\Finder\Finder;

class UsageFinderFactory implements FinderFactoryInterface
{
    public function createFinder()
    {
        $finder = new Finder();
        $finder
            ->name('*.php')
            ->exclude('vendor')
            ->exclude('Tests')
            ->exclude('Test');

        return $finder;
    }
}
