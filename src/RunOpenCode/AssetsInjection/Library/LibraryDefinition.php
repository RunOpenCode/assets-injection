<?php

namespace RunOpenCode\AssetsInjection\Library;

use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Exception\IndexOutOfBoundException;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;

class LibraryDefinition implements LibraryDefinitionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $resources;

    public function __construct($name, array $resources = [])
    {
        $this->name = $name;
        $this->resources = $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setResources(array $resources)
    {
        $this->resources = array_values($resources);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * {@inheritdoc}
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceResource(ResourceInterface $resource, array $replacement)
    {
        if (count($replacement) == 0) {
            return $this->removeResource($resource);
        }

        $previousResources = $this->resources;
        $this->resources = [];
        $replacementCount = 0;

        foreach ($previousResources as $previousResource) {
            if ($previousResource == $resource) {

                $replacementCount++;

                foreach ($replacement as $replacementResource) {
                    $this->addResource($replacementResource);
                }
            } else {
                $this->addResource($previousResource);
            }
        }

        if ($replacementCount == 0) {
            throw new InvalidArgumentException(sprintf('Provided resource "%s" could not be replaced since it does not exist in definition "%s".', $resource->getSource(), $this->getName()));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeResource(ResourceInterface $resource)
    {
        $removalCount = 0;

        $this->resources = array_values(array_filter($this->resources, function($item) use ($resource, &$removalCount) {
            if ($item == $resource) {
                $removalCount++;
                return false;
            } else {
                return true;
            }
        }));

        if ($removalCount == 0) {
            throw new InvalidArgumentException(sprintf('Provided resource "%s" could not be removed since it does not exist in definition "%s".', $resource->getSource(), $this->getName()));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return array_key_exists($this->key(), $this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return reset($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->resources[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof ResourceInterface) {
            throw new InvalidArgumentException(sprintf('Instance of "%s" expected, "%s" given.', 'RunOpenCode\AssetsInjection\Contract\ResourceInterface', (is_object($value) ? get_class($value) : gettype($value))));
        }

        if (is_null($offset)) {
            $this->resources[] = $value;
        } elseif ($offset < 0 || $offset > $this->count()) {
            throw new IndexOutOfBoundException(sprintf('Invalid offset "%s" provided.', $offset));
        } else {
            $this->resources[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->resources[$offset]);
            $this->resources = array_values($this->resources);
        } else {
            throw new IndexOutOfBoundException(sprintf('Invalid offset "%s" provided.', $offset));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }
}