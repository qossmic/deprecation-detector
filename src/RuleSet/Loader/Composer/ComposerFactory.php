<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader\Composer;

use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileDoesNotExistsException;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileIsInvalidException;
use Symfony\Component\Finder\SplFileInfo;

class ComposerFactory
{
    /**
     * @param string $lockPath
     *
     * @return Composer
     *
     * @throws ComposerFileDoesNotExistsException
     * @throws ComposerFileIsInvalidException
     */
    public function fromLock($lockPath)
    {
        if (!is_file($lockPath)) {
            throw new ComposerFileDoesNotExistsException($lockPath);
        }

        $file = new SplFileInfo($lockPath, null, null);
        $decodedData = json_decode($file->getContents(), true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new ComposerFileIsInvalidException($lockPath);
        }

        $packages = [];
        foreach ($decodedData['packages'] as $package) {
            $packages[] = Package::fromArray($package);
        }

        $devPackages = [];
        foreach ($decodedData['packages-dev'] as $package) {
            $devPackages[] = Package::fromArray($package);
        }

        return new Composer($packages, $devPackages, true);
    }
}
