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
use RunOpenCode\AssetsInjection\Exception\RuntimeException;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\JavascriptStringResource;
use RunOpenCode\AssetsInjection\Resource\StylesheetStringResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class CheckContainerResourcesRenderability implements CompilerPassInterface
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

            foreach ($resources = $definition->getResources() as $resource) {
                if (
                    !$resource instanceof FileResource
                    &&
                    !$resource instanceof HttpResource
                    &&
                    !$resource instanceof JavascriptStringResource
                    &&
                    !$resource instanceof StylesheetStringResource
                ) {
                    throw new RuntimeException(sprintf('This container can not be rendered. Maybe you didn\'t included all necessary compiler passes?'));
                }
            }

        }

        return new CompilerPassResult($container);
    }
}