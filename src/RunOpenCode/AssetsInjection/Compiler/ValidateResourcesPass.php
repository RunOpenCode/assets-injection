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
use RunOpenCode\AssetsInjection\Exception\ConfigurationException;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Exception\RuntimeException;
use RunOpenCode\AssetsInjection\Exception\UnavailableResourceException;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Resource\StringResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class ValidateResourcesPass
 *
 * Validates provided resources for given definitions.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class ValidateResourcesPass implements CompilerPassInterface
{
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge_recursive([
            'check_filter_assignment' => true,
            'check_availability' => [
                'file_resource' => true,
                'glob_resource' => true,
                'http_resource' => true,
                'reference_resource' => true
            ]
        ], $options);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        $methods = array_filter(get_class_methods($this), function($method){
            return strpos($method, 'check') === 0;
        });

        /**
         * @var LibraryDefinitionInterface $definition
         */
        foreach ($definitions = $container->getLibraries()->getDefinitions() as $definition) {

            /**
             * @var ResourceInterface $resource
             */
            foreach ($resources = $definition->getResources() as $resource) {
                foreach ($methods as $method) {
                    call_user_func_array(array($this, $method), array($container, $definition, $resource));
                }
            }
        }

        return new CompilerPassResult($container);
    }

    /**
     * Check if filters are properly assigned to resource.
     *
     * @param ContainerInterface $container Current container.
     * @param LibraryDefinitionInterface $definition Current definition.
     * @param ResourceInterface $resource Current resource.
     * @throws ConfigurationException
     */
    private function checkFiltersAssignment(ContainerInterface $container, LibraryDefinitionInterface $definition, ResourceInterface $resource)
    {
        if ($this->options['check_filter_assignment']) {

            if (isset($resource->getOptions()['filters'])) {
                $filters = $resource->getOptions()['filters'];

                if (!is_array($filters)) {
                    throw new ConfigurationException(sprintf('Expected array of filters for assigned for resource "%s" in definition "%s", "%s" given.', $resource->getSource(), $definition->getName(), gettype($filters)));
                }

                if ($resource instanceof ReferenceResource) {
                    throw new ConfigurationException(sprintf('You can not assign filters for reference resource "%s" in definition "%s".', $resource->getSource(), $definition->getName()));
                }

                $filters = array_map(function($filter) {
                    return ltrim($filter, '?');
                }, $filters);

                if (count($duplicates = array_unique(array_diff_assoc($filters, array_unique($filters)))) > 0) {
                    throw new ConfigurationException(sprintf('Filters "%s" for resource "%s" in definition "%s" are used more than once.', implode('", "', $duplicates), $resource->getSource(), $definition->getName()));
                }
            }

        }
    }

    /**
     * Check if resource is available.
     *
     * @param ContainerInterface $container Current container.
     * @param LibraryDefinitionInterface $definition Current definition.
     * @param ResourceInterface $resource Current resource.
     * @throws UnavailableResourceException
     * @throws RuntimeException
     */
    public function checkResourceAvailability(ContainerInterface $container, LibraryDefinitionInterface $definition, ResourceInterface $resource)
    {
        if ($this->options['check_availability']) {

            if ($resource instanceof FileResource && $this->options['check_availability']['file_resource']) {


                if (!@file_exists($resource->getSource())) {
                    throw new UnavailableResourceException(sprintf('File resource "%s" does not exists.', $resource->getSource()));
                }

                if (!@is_file($resource->getSource())) {
                    throw new UnavailableResourceException(sprintf('File resource "%s" is not file.', $resource->getSource()));
                }

                if (!@is_readable($resource->getSource())) {
                    throw new UnavailableResourceException(sprintf('File resource "%s" is not readable.', $resource->getSource()));
                }

            } elseif ($resource instanceof GlobResource && $this->options['check_availability']['glob_resource']) {

                $files = $resource->getFiles();

                if (count($files) == 0) {
                    throw new InvalidArgumentException(sprintf('Glob resource "%s" yields no results.', $resource->getSource()));
                }

                foreach ($files as $file) {

                    if (!@file_exists($file)) {
                        throw new UnavailableResourceException(sprintf('Glob "%s" resulting file "%s" does not exists.', $resource->getSource(), $file));
                    }

                    if (!@is_file($file)) {
                        throw new UnavailableResourceException(sprintf('Glob "%s" result "%s" is not file.', $resource->getSource(), $file));
                    }

                    if (!@is_readable($file)) {
                        throw new UnavailableResourceException(sprintf('Glob "%s" resulting file "%s" is not readable.', $resource->getSource(), $file));
                    }
                }

            } elseif ($resource instanceof HttpResource && $this->options['check_availability']['http_resource']) {

                if (strpos(($url = $resource->getSource()), '//') === 0) {
                    $url = 'http:' . $url;
                } elseif (strpos($url, '://') === false) {
                    throw new InvalidArgumentException(sprintf('Http resource "%s" is not valid.', $resource->getSource()));
                }

                if (@file_get_contents($url, false, stream_context_create(array('http' => array('method' => 'HEAD')))) === false) {
                    throw new UnavailableResourceException(sprintf('Http resource "%s" is not available.', $resource->getSource()));
                }

            } elseif ($resource instanceof ReferenceResource && $this->options['check_availability']['reference_resource']) {

                if (!$container->getLibraries()->hasDefinition($resource->getSource())) {
                    throw new UnavailableResourceException(sprintf('Reference resource "%s" references to non-existing library.', $resource->getSource()));
                }

            } elseif ($resource instanceof StringResource) {

                // Always available

            } else {
                throw new RuntimeException(sprintf('Unable to determine resource availability of instance of "%s".', get_class($resource)));
            }
        }
    }
}