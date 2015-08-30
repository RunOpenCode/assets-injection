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

/**
 * Interface CompilerInterface
 *
 * Defines Compiler which holds all registered compilers and executes Container compilation process.
 *
 * @package RunOpenCode\AssetsInjection\Contract
 */
interface CompilerInterface
{
    /**
     * Add compiler pass to compiler.
     *
     * @param CompilerPassInterface $compilerPass Compiler pass to add.
     * @param int $priority Priority of execution in compilation chain.
     * @return CompilerInterface $this Fluent interface.
     */
    public function addCompilerPass(CompilerPassInterface $compilerPass, $priority = 0);

    /**
     * Execute all compiler passes in compilation chain against provided container.
     *
     * @param ContainerInterface $container Container to compile.
     * @return ContainerInterface Compiled container.
     */
    public function compile(ContainerInterface $container);
}