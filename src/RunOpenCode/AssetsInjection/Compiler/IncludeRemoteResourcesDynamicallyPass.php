<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Compiler;

use RunOpenCode\AssetsInjection\Contract\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Exception\RuntimeException;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\JavascriptStringResource;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class IncludeRemoteResourcesDynamicallyPass
 *
 * Replaces remotely included assets from CDN with dynamic JS load on client.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class IncludeRemoteResourcesDynamicallyPass implements CompilerPassInterface
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'include' => ['js', 'css'],
            'whitelist' => [],
            'blacklist' => [],
            'helper' => realpath(__DIR__ . '/../Public/js/RunOpenCode.DynamicAssetInclusion.min.js')
        ], $options);

        if (count($this->options['whitelist']) > 0 && count($this->options['blacklist'])) {
            throw new InvalidArgumentException('You can not have both "whitelist" and "blacklist" configured.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        if (count($this->options['include'])) {
            /**
             * @var LibraryDefinitionInterface $definition
             */
            foreach ($definitions = $container->getLibraries()->getDefinitions() as $definition) {

                $hasHelper = false;
                /**
                 * @var ResourceInterface $resource
                 */
                foreach ($resources = $definition->getResources() as $resource) {

                    if (
                        $resource instanceof HttpResource
                        &&
                        in_array(AssetType::guessExtension($resource->getSource()), $this->options['include'])
                        &&
                        ($replacement = $this->processResource($resource)) != $resource
                    ) {
                        if ($hasHelper) {
                            $definition->replaceResource($resource, [
                                $replacement
                            ]);
                        } else {
                            $hasHelper = true;
                            $definition->replaceResource($resource, [
                                new FileResource($this->options['helper']),
                                $replacement
                            ]);
                        }
                    }
                }
            }
        }

        return new CompilerPassResult($container);
    }

    /**
     * Process given remote resource, and if it is eligible for dynamic load, it is replaced with javascript
     * function for inclusion on client side.
     *
     * @param ResourceInterface $resource Resource to process.
     * @return ResourceInterface New resource with dynamic js code, or same resource if it is blacklisted.
     */
    private function processResource(ResourceInterface $resource)
    {
        $url = $resource->getSource();

        if (
            (count($this->options['whitelist']) && !in_array($url, $this->options['whitelist']))
                ||
            (count($this->options['blacklist']) && in_array($url, $this->options['blacklist']))
        ) {
            return $resource;
        }

        switch (AssetType::guessAssetType($url)) {
            case AssetType::JAVASCRIPT:
                return new JavascriptStringResource(sprintf(';(function(r){ r.DynamicAssetInclusion.loadJavascript(\'%s\'); })(RunOpenCode);', addslashes($url)));
                break;
            case AssetType::STYLESHEET:
                return new JavascriptStringResource(sprintf(';(function(r){ r.DynamicAssetInclusion.loadStylesheet(\'%s\', %s); })(RunOpenCode);', addslashes($url), ((isset($resource->getOptions()['media'])) ? sprintf('\'%s\'', $resource->getOptions()['media']) : 'null')));
                break;
            default:
                throw new RuntimeException(sprintf('Unknown remote resource type with source "%s".', $resource->getSource()));
                break;
        }
    }
}