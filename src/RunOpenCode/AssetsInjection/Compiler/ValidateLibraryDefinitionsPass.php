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
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class ValidateLibraryDefinitionsPass
 *
 * Validates library definitions in container.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class ValidateLibraryDefinitionsPass implements CompilerPassInterface
{
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

            foreach ($methods as $method) {
                call_user_func_array(array($this, $method), array($container, $definition));
            }
        }

        return new CompilerPassResult($container);
    }

    /**
     * Check if definition does not have any resource.
     *
     * @param ContainerInterface $container Current container.
     * @param LibraryDefinitionInterface $definition Current definition.
     */
    private function checkIsDefinitionEmpty(ContainerInterface $container, LibraryDefinitionInterface $definition)
    {
        if (count($definition->getResources()) == 0) {
            throw new InvalidArgumentException(sprintf('Library definition "%s" does not contain any resource.', $definition->getName()));
        }
    }

    /**
     * Check if definition name is valid.
     *
     * @param ContainerInterface $container Current container.
     * @param LibraryDefinitionInterface $definition Current definition.
     */
    public function checkDefinitionName(ContainerInterface $container, LibraryDefinitionInterface $definition)
    {
        if (!ctype_alnum(str_replace(array('-', '_', '/'), '', $definition->getName()))) {
            throw new InvalidArgumentException(sprintf('Library definition name "%s" is not valid.', $definition->getName()));
        }
    }

}