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
use Countable;
use Iterator;
use ArrayAccess;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;

/**
 * Interface LibraryDefinitionInterface
 *
 * LibraryDefinition of one single assets library that which can consist of JavaScript and CSS files,
 * as well as other asset libraries.
 *
 * @package RunOpenCode\AssetsInjection\Contract
 */
interface LibraryDefinitionInterface extends Countable, Iterator, ArrayAccess
{
    /**
     * Get unique asset library name.
     *
     * @return string
     */
    public function getName();

    /**
     * Add resource to the asset library.
     *
     * @param ResourceInterface $resource       Resource to add to library definition.
     * @return LibraryDefinitionInterface $this        Fluent interface.
     */
    public function addResource(ResourceInterface $resource);

    /**
     * Set resources for asset library.
     *
     * Set resources for asset library, removing resources previously added to collection.
     *
     * @param array<ResourceInterface> $resources       Collection of resources.
     * @return LibraryDefinitionInterface $this                Fluent interface.
     */
    public function setResources(array $resources);

    /**
     * Get resources for library.
     *
     * @return array<ResourceInterface>         Collection of resources.
     */
    public function getResources();

    /**
     * Replace provided resource with provided resources.
     *
     * @param ResourceInterface $resource               Resource to replace.
     * @param array $replacement                        Replacement resources.
     * @return LibraryDefinitionInterface $this                Fluent interface.
     * @throws InvalidArgumentException                 If resource does not exist in definition.
     */
    public function replaceResource(ResourceInterface $resource, array $replacement);

    /**
     * Remove given resource from definition.
     *
     * @param ResourceInterface $resource               Resource to remove.
     * @return LibraryDefinitionInterface $this                Fluent interface.
     * @throws InvalidArgumentException                 If resource does not exist in definition.
     */
    public function removeResource(ResourceInterface $resource);
}