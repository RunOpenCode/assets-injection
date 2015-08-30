<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection;

use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryCollectionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Resource\JavascriptStringResource;
use RunOpenCode\AssetsInjection\Resource\StylesheetStringResource;
use RunOpenCode\AssetsInjection\Utils\AssetType;

class Container implements ContainerInterface
{
    /**
     * @var LibraryCollectionInterface
     */
    protected $libraries;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array<AssetInterface>  Injected definitions.
     */
    protected $injected;

    /**
     * A constructor.
     *
     * @param LibraryCollectionInterface $libraries Collection of libraries.
     * @param array $options Container options.
     */
    public function __construct(LibraryCollectionInterface $libraries, array $options = [])
    {
        $this->libraries = $libraries;
        $this->options = array_merge([], $options);
        $this->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function setLibraries(LibraryCollectionInterface $manager)
    {
        $this->libraries = $manager;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraries()
    {
        return $this->libraries;
    }

    /**
     * {@inheritdoc}
     */
    public function inject($name)
    {
        if (isset($this->injected['libraries'][$name])) {
            return $this;
        }

        $this->injected['libraries'][$name] = $this->libraries->getDefinition($name);

        /**
         * @var ResourceInterface $resource
         */
        foreach ($this->injected['libraries'][$name]->getResources() as $resource) {
            if (!isset($this->injected['resources'][$resource->getKey()])) {
                $this->injected['resources'][$resource->getKey()] = $resource;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eject($type, $position = null)
    {
        if (!in_array(($type = strtolower($type)), [
            AssetType::JAVASCRIPT,
            AssetType::STYLESHEET
        ])) {
            throw new InvalidArgumentException(sprintf('You can not eject resources of unknown "%s" asset type.', $type));
        }

        $ejected = [];

        foreach ($this->injected['resources'] as $key => $value) {
            /**
             * @var ResourceInterface $value
             */
            if (
                !is_null($value) // Eject if resource is not already ejected
                &&
                (
                    $position === false
                    ||
                    (
                        is_null($position) && !isset($value->getOptions()['position'])
                    )
                    ||
                    (
                        $value->getOptions()['position'] == $position
                    )
                ) // It is on matching position
                &&
                (
                    ($type == AssetType::JAVASCRIPT && $value instanceof JavascriptStringResource)
                    ||
                    ($type == AssetType::STYLESHEET && $value instanceof StylesheetStringResource)
                    ||
                    ($type == AssetType::guessAssetType($value))
                ) // And it is of proper type

            ) {
                $ejected[$key] = $value;
                $this->injected['resources'][$key] = null;
            }
        }

        return $ejected;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->injected = [
            'libraries' => [],
            'resources' => []
        ];
    }

}