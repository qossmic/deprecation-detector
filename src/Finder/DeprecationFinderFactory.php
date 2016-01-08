<?php

namespace SensioLabs\DeprecationDetector\Finder;

use Symfony\Component\Finder\Finder;

class DeprecationFinderFactory implements FinderFactoryInterface
{
    public function createFinder()
    {
        $finder = new Finder();
        $finder
            ->name('*.php')
            ->contains('@deprecated')
            ->exclude('vendor')
            ->exclude('Tests')
            ->exclude('Test');

        return $finder;
    }
}

