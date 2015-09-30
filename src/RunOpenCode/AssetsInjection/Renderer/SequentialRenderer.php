<?php

namespace RunOpenCode\AssetsInjection\Renderer;

use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Exception\RuntimeException;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use RunOpenCode\AssetsInjection\Value\PathType;

class SequentialRenderer extends AbstractRenderer
{
    public function render(array $resources, array $options = [])
    {
        $result = [];

        /**
         * @var ResourceInterface $resource
         */
        foreach ($resources as $resource) {

            $attributes = array_merge(
                [],
                ((isset($options['attributes'])) ? $options['attributes'] : []),
                ((isset($resource->getOptions()['attributes'])) ? $resource->getOptions()['attributes'] : [])
            );

            switch ($resourceClass = ltrim(get_class($resource), '\\')) {
                case 'RunOpenCode\AssetsInjection\Resource\JavascriptStringResource':
                    $result[] = $this->getJavascriptCodeHtml($resource->getSource(), $attributes);
                    break;
                case 'RunOpenCode\AssetsInjection\Resource\StylesheetStringResource':
                    $result[] = $this->getStylesheetCodeHtml($resource->getSource(), $attributes);
                    break;
                case 'RunOpenCode\AssetsInjection\Resource\HttpResource':
                    switch ($type = AssetType::guessAssetType($resource)) {
                        case AssetType::JAVASCRIPT:
                            $result[] = $this->getJavascriptIncludeHtml($resource->getSource(), $attributes);
                            break;
                        case AssetType::STYLESHEET:
                            $result[] = $this->getStylesheetIncludeHtml($resource->getSource(), $attributes);
                            break;
                        default:
                            throw new RuntimeException(sprintf('Unsupported type "%s".', $type));
                            break;
                    }
                    break;
                case 'RunOpenCode\AssetsInjection\Resource\FileResource':

                    switch ($options['path_type']) {
                        case PathType::RELATIVE:
                            $path = str_replace($options['web_root'], '', $resource->getSource());
                            break;
                        case PathType::ABSOLUTE:
                            $path = $options['http_root'] . str_replace($options['web_root'], '', $resource->getSource());
                            break;
                        case PathType::RAW:
                            $path = $resource->getSource();
                            break;
                        default:
                            throw new InvalidArgumentException(sprintf('Unknown path type "%s".', $options['path_type']));
                            break;
                    }

                    switch ($type = AssetType::guessAssetType($resource)) {
                        case AssetType::JAVASCRIPT:
                            $result[] = $this->getJavascriptIncludeHtml($path, $attributes);
                            break;
                        case AssetType::STYLESHEET:
                            $result[] = $this->getStylesheetIncludeHtml($path, $attributes);
                            break;
                        default:
                            throw new RuntimeException(sprintf('Unsupported type "%s".', $type));
                            break;
                    }

                    break;
                default:
                    throw new RuntimeException(sprintf('Unsupported resource "%s".', $resourceClass));
                    break;
            }
        }

        return implode("\n", $result);
    }
}