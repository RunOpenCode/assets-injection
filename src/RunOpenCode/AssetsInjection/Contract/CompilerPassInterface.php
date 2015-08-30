<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Contract;

use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Interface CompilerPassInterface
 *
 * Compiler pass is in charge to compile and process ContainerInterface with purpose of modifying and optimizing
 * asset injection process.
 *
 * @package RunOpenCode\AssetsInjection\Contract
 */
interface CompilerPassInterface
{
    /**
     * Process container and its definitions.
     *
     * Process Container and its definitions, compiling its settings, executing validations and optimizations.
     * Compiler pass can replace current Container with some other Container implementation, as well it can
     * stop further processing of the Container with other compilers.
     *
     * @param ContainerInterface $container Container to compile.
     * @return CompilerPassResult Compilation result.
     */
    public function process(ContainerInterface $container);
}