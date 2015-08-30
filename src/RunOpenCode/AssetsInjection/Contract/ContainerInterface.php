<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Contract;

/**
 * Interface ContainerInterface
 *
 * Container is collection of asset library definitions.
 *
 * @package RunOpenCode\AssetsInjection\Contract
 */
interface ContainerInterface
{
    /**
     * Set collection of asset libraries.
     *
     * @param LibraryCollectionInterface $libraries Defined asset libraries.
     * @return ContainerInterface $this Fluent interface.
     */
    public function setLibraries(LibraryCollectionInterface $libraries);

    /**
     * Get collection of asset libraries.
     *
     * @return LibraryCollectionInterface
     */
    public function getLibraries();

    /**
     * Inject assets library in current container.
     *
     * @param string $name LibraryDefinition name.
     * @return ContainerInterface $this Fluent interface.
     */
    public function inject($name);

    /**
     * Eject injected resources from current container.
     *
     * Eject injected resources from current container for rendering (or any other purpose), but prevent them to be
     * injected again.
     *
     * @param string $type Resource asset type to eject.
     * @param string|null $position Position of resource, if any.
     * @return array Ejected resources.
     */
    public function eject($type, $position = null);

    /**
     * Remove all injected assets from current container.
     *
     * @return ContainerInterface $this Fluent interface.
     */
    public function clear();

}