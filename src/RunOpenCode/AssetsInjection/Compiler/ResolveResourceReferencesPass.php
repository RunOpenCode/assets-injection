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
use RunOpenCode\AssetsInjection\Exception\CircularReferenceException;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class ResolveResourceReferencesPass
 *
 * Resolve definition dependencies in order to optimize injection.
 *
 * Notes:
 *  - Implemented algorithm processes tree of dependencies by using bottom-up approach via recursive function. That means
 *    that in tree, nodes without dependencies are first to process, then nodes that referencing to other nodes gets processed.
 *  - Implemented algorithm can detect circular reference and it will not get stuck into infinite loop. However, it will not
 *    provide too much info where circular reference is detected. In order to get additional details about circular reference, you can use
 *    "\RunOpenCode\AssetsInjection\Compiler\CheckCircularReferencesPass" that can be executed prior to this compiler pass.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class ResolveResourceReferencesPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        $processedDefinitions = [];

        /**
         * @var LibraryDefinitionInterface $definition
         */
        foreach ($definitions = $container->getLibraries()->getDefinitions() as $definition) {

            $this->processDefinition($definition, $container, $processedDefinitions);

        }

        return new CompilerPassResult($container);
    }

    /**
     * Process one single library definition resolving referenced dependencies.
     *
     * @param LibraryDefinitionInterface $definition Library definition to process.
     * @param ContainerInterface $container Container which is subject of process.
     * @param array $processedDefinitions List of already processed definitions.
     * @param int $count Number of processed definition for detecting circular references.
     * @throws CircularReferenceException If circular reference is detected.
     */
    private function processDefinition(LibraryDefinitionInterface $definition, ContainerInterface $container, array &$processedDefinitions, &$count = 0)
    {
        $count++;

        if ($count > count($container->getLibraries()->getDefinitions())) {
            throw new CircularReferenceException('Circular reference detected.');
        }
        /**
         * @var ResourceInterface $resource
         */
        foreach ($resources = $definition->getResources() as $resource) {

            /**
             * @var ReferenceResource $resource
             */
            if ($resource instanceof ReferenceResource) {

                if (!isset($processedDefinitions[$resource->getSource()])) {
                    $this->processDefinition($container->getLibraries()->getDefinition($resource->getSource()), $container, $processedDefinitions, $count);
                }

                $definition->replaceResource($resource, array_filter($this->getReferencedResources($resource, $container), function(ResourceInterface $replacement) use ($definition) {
                    return !in_array($replacement, $definition->getResources());
                }));

            }
        }

        $processedDefinitions[$definition->getName()] = $definition->getName();
    }

    /**
     * Get referenced resources for referencing resource.
     *
     * @param ReferenceResource $referencingResource        Referencing resource to replace.
     * @param ContainerInterface $container                 Container which is subject of process.
     * @return array                                        Collection of concrete resources.
     */
    private function getReferencedResources(ReferenceResource $referencingResource, ContainerInterface $container)
    {
        $result = [];

        $definition = $container->getLibraries()->getDefinition($referencingResource->getSource());

        /**
         * @var ResourceInterface $resource
         */
        foreach ($resources = $definition->getResources() as $resource) {

            if (!in_array($resource, $result)) {
                $result[] = $resource;
            }
        }

        return $result;
    }
}