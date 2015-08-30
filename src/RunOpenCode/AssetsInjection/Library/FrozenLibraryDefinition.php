<?php

namespace RunOpenCode\AssetsInjection\Library;

use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Exception\LogicException;

/**
 * Class FrozenLibraryDefinition
 *
 * Frozen library definition disables its mutation after creation.
 *
 * @package RunOpenCode\AssetsInjection\Library
 */
class FrozenLibraryDefinition extends LibraryDefinition
{
    /**
     * @var bool Denotes possibility of invocation of methods for definition mutation.
     */
    private $frozen = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, array $resources = [])
    {
        parent::__construct($name, $resources);
        $this->frozen = true;
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If definition is frozen for mutations.
     */
    public function setResources(array $resources)
    {
        if ($this->frozen) {
            throw new LogicException('Library definition can not be mutated in this context.');
        }

        return parent::setResources($resources);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If definition is frozen for mutations.
     */
    public function addResource(ResourceInterface $resource)
    {
        if ($this->frozen) {
            throw new LogicException('Library definition can not be mutated in this context.');
        }

        return parent::addResource($resource);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If definition is frozen for mutations.
     */
    public function replaceResource(ResourceInterface $resource, array $replacement)
    {
        if ($this->frozen) {
            throw new LogicException('Library definition can not be mutated in this context.');
        }

        return parent::replaceResource($resource, $replacement);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If definition is frozen for mutations.
     */
    public function removeResource(ResourceInterface $resource)
    {
        if ($this->frozen) {
            throw new LogicException('Library definition can not be mutated in this context.');
        }

        return parent::removeResource($resource);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If definition is frozen for mutations.
     */
    public function offsetSet($offset, $value)
    {
        if ($this->frozen) {
            throw new LogicException('Library definition can not be mutated in this context.');
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritdoc}
     * @throws LogicException If definition is frozen for mutations.
     */
    public function offsetUnset($offset)
    {
        if ($this->frozen) {
            throw new LogicException('Library definition can not be mutated in this context.');
        }

        parent::offsetUnset($offset);
    }
}