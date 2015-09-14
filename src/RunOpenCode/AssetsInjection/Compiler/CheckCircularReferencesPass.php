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
 * Class CheckCircularReferencesPass
 *
 * Class which checks for circular references between Definitions within the provided Container.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class CheckCircularReferencesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        /**
         * @var LibraryDefinitionInterface $definition
         */
        foreach ($definitions = $container->getLibraries()->getDefinitions() as $name => $definition) {
            $this->doCheckCircularReferences($container, $definition, array($name => $name));
        }

        return new CompilerPassResult($container);
    }

    /**
     * Checks for circular references within the definitions in ContainerInterface using recursive function and
     * pointer to processed definitions list.
     *
     * @param ContainerInterface $container Container to check.
     * @param LibraryDefinitionInterface $definition Library definition to process.
     * @param array $registeredReferences List of already processed definitions in path.
     * @throws CircularReferenceException If circular reference is detected.
     */
    private function doCheckCircularReferences(ContainerInterface $container, LibraryDefinitionInterface $definition, array $registeredReferences)
    {
        /**
         * @var ResourceInterface $resource
         */
        foreach ($resources = $definition->getResources() as $resource) {

            /**
             * @var ReferenceResource $resource
             */
            if ($resource instanceof ReferenceResource) {

                if (array_key_exists($resource->getSource(), $registeredReferences)) {
                    throw new CircularReferenceException(sprintf('Circular reference detected at definition "%s", dependency chain (path: [%s] => [%s]).', $resource->getSource(), implode('] => [', $registeredReferences), $resource->getSource()));
                } else {
                    $newPath = $registeredReferences;
                    $newPath[$resource->getSource()] = $resource->getSource();
                    $this->doCheckCircularReferences($container, $container->getLibraries()->getDefinition($resource->getSource()), $newPath);
                }
            }
        }
    }
}