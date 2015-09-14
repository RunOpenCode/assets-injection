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

use RunOpenCode\AssetsInjection\Contract\Compiler\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class ProcessGlobResourcesPass
 *
 * Process glob resources creating file resources from glob string.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class ProcessGlobResourcesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        /**
         * @var LibraryDefinitionInterface $definition
         */
        foreach ($definitions = $container->getLibraries()->getDefinitions() as $definition) {

            /**
             * @var ResourceInterface $resource
             */
            foreach ($resources = $definition->getResources() as $resource) {

                if ($resource instanceof GlobResource) {

                    $files = glob($resource->getSource());

                    if (count($files) > 0) {

                        $globResources = [];

                        foreach ($files as $file) {
                            $globResources[] = new FileResource($file, $resource->getOptions());
                        }

                        $definition->replaceResource($resource, $globResources);

                    } else {
                        throw new \InvalidArgumentException(sprintf('glob() pattern "%s" for definition "%s" yields no results.', $resource->getSource(), $definition->getName()));
                    }
                }

            }
        }

        return new CompilerPassResult($container);
    }
}