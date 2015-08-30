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
use RunOpenCode\AssetsInjection\Exception\LogicException;

/**
 * Interface LibraryCollectionInterface
 *
 * Defines manager for library definitions.
 *
 * @package RunOpenCode\AssetsInjection\Contract
 */
interface LibraryCollectionInterface extends Countable, Iterator, ArrayAccess
{
    /**
     * Add definition to manager.
     *
     * @param LibraryDefinitionInterface $definition       LibraryDefinition to add.
     * @return LibraryCollectionInterface           Fluent interface.
     * @throws LogicException                       If definition already exist in collection.
     */
    public function addDefinition(LibraryDefinitionInterface $definition);

    /**
     * Check if definition exist in collection.
     *
     * @param string $name      LibraryDefinition to search for.
     * @return bool             TRUE if definition exist in collection.
     */
    public function hasDefinition($name);

    /**
     * Get definition from the collection.
     *
     * @param string $name                      Name of the definition.
     * @return LibraryDefinitionInterface              A requested definition.
     * @throws LogicException                   If definition does not exist in collection.
     */
    public function getDefinition($name);

    /**
     * Get all definitions from manager.
     *
     * @return array<LibraryDefinitionInterface>   Collection of definitions.
     */
    public function getDefinitions();

    /**
     * Remove definition from collection.
     *
     * @param string $name                      Name of the definition to remove.
     * @return LibraryCollectionInterface       Fluent interface.
     * @throws LogicException                   If definition does not exist in collection.
     */
    public function removeDefinition($name);
}