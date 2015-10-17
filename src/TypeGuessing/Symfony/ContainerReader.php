<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class ContainerReader.
 */
class ContainerReader
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @param string $containerPath
     *
     * @return bool
     */
    public function loadContainer($containerPath)
    {
        $containerPath = realpath($containerPath);

        if (false === $containerPath) {
            return false;
        }

        $this->container = new ContainerBuilder();

        try {
            $loader = new XmlFileLoader($this->container, new FileLocator(dirname($containerPath)));
            $loader->load(basename($containerPath));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $id
     *
     * @return bool|null
     */
    public function has($id)
    {
        if (null === $this->container) {
            return;
        }

        return $this->container->hasDefinition($id);
    }

    /**
     * @param $id
     *
     * @return string|null
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            return;
        }

        return $this->container->findDefinition($id)->getClass();
    }

    /**
     * @param $name
     * @param $args
     *
     * @throws \BadMethodCallException
     */
    public function __call($name, $args)
    {
        throw new \BadMethodCallException(
            'Unlike Symfony container SymfonyContainerReader is read only and just implements the '
            .'methods "has" and "get".'
        );
    }
}
