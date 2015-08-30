<?php

namespace RunOpenCode\AssetsInjection\Library;

use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Exception\LogicException;

/**
 * Class FrozenLibraryCollection
 *
 * Frozen library collection disables mutation of its elements after construction.
 *
 * @package RunOpenCode\AssetsInjection\Library
 */
class FrozenLibraryCollection extends LibraryCollection
{
    /**
     * @var bool Denotes possibility of invocation of methods for collection mutation.
     */
    private $frozen = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $definitions = [])
    {
        parent::__construct($definitions);
        $this->frozen = true;
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If collection is frozen for mutations.
     */
    public function addDefinition(LibraryDefinitionInterface $definition)
    {
        if ($this->frozen) {
            throw new LogicException('Library collection can not be mutated in this context.');
        }

        return parent::addDefinition($definition);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If collection is frozen for mutations.
     */
    public function addDefinitions(array $definitions)
    {
        if ($this->frozen) {
            throw new LogicException('Library collection can not be mutated in this context.');
        }

        return parent::addDefinitions($definitions);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If collection is frozen for mutations.
     */
    public function removeDefinition($name)
    {
        if ($this->frozen) {
            throw new LogicException('Library collection can not be mutated in this context.');
        }

        return parent::removeDefinition($name);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If collection is frozen for mutations.
     */
    public function removeDefinitions()
    {
        if ($this->frozen) {
            throw new LogicException('Library collection can not be mutated in this context.');
        }

        return parent::removeDefinitions();
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If collection is frozen for mutations.
     */
    public function offsetSet($offset, $value)
    {
        if ($this->frozen) {
            throw new LogicException('Library collection can not be mutated in this context.');
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If collection is frozen for mutations.
     */
    public function offsetUnset($offset)
    {
        if ($this->frozen) {
            throw new LogicException('Library collection can not be mutated in this context.');
        }

        parent::offsetUnset($offset);
    }
}