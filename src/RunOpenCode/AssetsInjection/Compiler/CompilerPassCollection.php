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

use Countable;
use Iterator;
use RunOpenCode\AssetsInjection\Contract\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class CompilerPassCollection
 *
 * Collection of compiler passes, supporting assigning compiling priority.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class CompilerPassCollection implements CompilerPassInterface, Countable, Iterator
{
    /**
     * @var array
     */
    private $compilers;

    /**
     * @var array<CompilerPassInterface>
     */
    private $iterator;

    public function __construct(array $compilers = [])
    {
        $this->compilers = [];
        /**
         * @var CompilerPassInterface $compiler
         */
        foreach ($compilers as $compiler) {
            $this->addCompiler($compiler);
        }
    }

    /**
     * Add compiler pass to collection.
     *
     * @param CompilerPassInterface $compiler Compiler pass to add.
     * @param int $priority Priority of execution.
     * @return CompilerPassCollection $this Fluid interface.
     */
    public function addCompiler(CompilerPassInterface $compiler, $priority = 0)
    {
        if (!array_key_exists($priority, $this->compilers)) {
            $this->compilers[$priority] = [];
        }

        $this->compilers[$priority][] = $compiler;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        $result = new CompilerPassResult($container);

        /**
         * @var CompilerPassInterface $compiler
         */
        foreach ($this as $compiler) {
            $result = $compiler->process($container);

            if ($result->isProcessingStopped()) {
                return $result;
            } else {
                $container = $result->getContainer();
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $result = 0;

        foreach ($this->compilers as $priority => $compilers) {
            $result += count($compilers);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return array_key_exists($this->key(), $this->iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        ksort($this->compilers);

        $this->iterator = [];

        foreach ($this->compilers as $priority => $compilers) {
            $this->iterator = array_merge(array_values($this->iterator), array_values($compilers));
        }

        reset($this->iterator);
    }
}