<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection;

use RunOpenCode\AssetsInjection\Compiler\CompilerPassCollection;
use RunOpenCode\AssetsInjection\Contract\Compiler\CompilerInterface;
use RunOpenCode\AssetsInjection\Contract\Compiler\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;

/**
 * Class Compiler
 *
 * Compiler compiles container applying registered compiler passes order by priority of execution in ascending order.
 *
 * @package RunOpenCode\AssetsInjection
 */
final class Compiler implements CompilerInterface
{
    /**
     * @var CompilerPassCollection
     */
    private $compilers;

    public function __construct()
    {
        $this->compilers = new CompilerPassCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addCompilerPass(CompilerPassInterface $compilerPass, $priority = 0)
    {
        $this->compilers->addCompiler($compilerPass, $priority);
        return $this;
    }

    /**
     * Add compiler passes to compiler.
     *
     * @param array<CompilerPassInterface> $compilerPasses Compiler passes to add.
     * @param int $priority Execution priority of added compiler passes.
     * @return CompilerInterface $this Fluent interface.
     */
    public function addCompilerPasses(array $compilerPasses = [], $priority = 0)
    {
        foreach ($compilerPasses as $compilerPass) {
            $this->addCompilerPass($compilerPass, $priority);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(ContainerInterface $container)
    {
        return $this->compilers->process($container);
    }
}