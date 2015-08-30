<?php

namespace RunOpenCode\AssetsInjection;

use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\ManagerInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceRendererInterface;
use RunOpenCode\AssetsInjection\Exception\ConfigurationException;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use RunOpenCode\AssetsInjection\Value\PathType;

class Manager implements ManagerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ResourceRendererInterface
     */
    private $resourceRenderer;

    /**
     * @var array
     */
    private $options;

    public function __construct(ContainerInterface $container, ResourceRendererInterface $resourceRenderer, array $options = [])
    {
        $this->container = $container;
        $this->resourceRenderer = $resourceRenderer;
        $this->options = array_merge(['path_type' => PathType::RELATIVE], $options);

        if (!array_key_exists('web_root', $this->options)) {
            throw new ConfigurationException('You have to provide "web_root" configuration parameter.');
        }

        if (!array_key_exists('http_root', $this->options)) {
            throw new ConfigurationException('You have to provide "http_root" configuration parameter.');
        }

        $this->options['web_root'] = rtrim($this->options['web_root'], '/');
        $this->options['http_root'] = rtrim($this->options['http_root'], '/');
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    public function setResourceRenderer(ResourceRendererInterface $resourceRenderer)
    {
        $this->resourceRenderer = $resourceRenderer;
        return $this;
    }

    public function inject($name)
    {
        $this->container->inject($name);
        return $this;
    }

    public function render($type, $position = null, array $options = [])
    {
        if (!in_array($type = strtolower($type), array(
            AssetType::JAVASCRIPT,
            AssetType::STYLESHEET
        ))) {
            throw new InvalidArgumentException(sprintf('Unsupported asset type "".', $type));
        }

        return $this->resourceRenderer->render($this->container->eject($type, $position), array_merge($this->options, $options));
    }
}