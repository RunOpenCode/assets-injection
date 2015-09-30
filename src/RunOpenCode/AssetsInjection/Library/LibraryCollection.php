<?php

namespace RunOpenCode\AssetsInjection\Library;

use RunOpenCode\AssetsInjection\Contract\LibraryCollectionInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Exception\LogicException;

class LibraryCollection implements LibraryCollectionInterface
{
    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * A constructor.
     *
     * @param array<LibraryDefinitionInterface> $definitions       Definitions to initialy add to collection.
     */
    public function __construct(array $definitions = [])
    {
        $this->addDefinitions($definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function addDefinition(LibraryDefinitionInterface $definition)
    {
        if ($this->hasDefinition($definition->getName())) {
            throw new LogicException(sprintf('LibraryDefinition with name "%s" is already defined and added to "%s".', $definition->getName(), get_class($this)));
        }

        $this->definitions[$definition->getName()] = $definition;

        return $this;
    }

    /**
     * Add collection of definitions at once.
     *
     * @param array<LibraryDefinitionInterface> $definitions       Definitions to add.
     * @return LibraryCollectionInterface                   Fluent interface.
     * @throws LogicException                               If definition to add already exsists in collection.
     */
    public function addDefinitions(array $definitions)
    {
        foreach ($definitions as $definition) {
            $this->addDefinition($definition);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefinition($name)
    {
        return array_key_exists($name, $this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition($name)
    {
        if (!$this->hasDefinition($name)) {
            throw new LogicException(sprintf('LibraryDefinition with name "%s" does not exist in "%s".', $name, get_class($this)));
        }

        return $this->definitions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function removeDefinition($name)
    {
        if (!$this->hasDefinition($name)) {
            throw new LogicException(sprintf('LibraryDefinitionInterface with provided name "%s" does not exist.', $name));
        }

        unset($this->definitions[$name]);

        return $this;
    }

    /**
     * Remove all definitions from the collection.
     *
     * @return LibraryCollectionInterface       Fluent interface.
     */
    public function removeDefinitions()
    {
        $this->definitions = [];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return array_key_exists($this->key(), $this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return reset($this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->definitions);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->definitions[$offset];
        } else {
            throw new InvalidArgumentException(sprintf('Unknown definition name "%s" provided.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof LibraryDefinitionInterface) {
            throw new InvalidArgumentException(sprintf('Instance of "%s" expected, "%s" given.', 'RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface', (is_object($value) ? get_class($value) : gettype($value))));
        }

        if (!is_null($offset)) {
            throw new InvalidArgumentException(sprintf('You can not provide offset for definition in definition manager.'));
        }

        $this->definitions[$value->getName()] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->definitions[$offset]);
        } else {
            throw new InvalidArgumentException(sprintf('Unknown definition name "%s" provided.'));
        }
    }
}