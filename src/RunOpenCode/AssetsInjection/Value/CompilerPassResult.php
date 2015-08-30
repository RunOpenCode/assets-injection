<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Value;

use RunOpenCode\AssetsInjection\Contract\ContainerInterface;

/**
 * Class CompilerPassResult
 *
 * Value class of compiler pass result.
 *
 * @package RunOpenCode\AssetsInjection\Value
 */
final class CompilerPassResult
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var bool
     */
    private $processingStopped;

    /**
     * A constructor.
     *
     * @param ContainerInterface $container Resulting container.
     * @param bool|false $processingStopped TRUE if compilation chain should be stopped. Default is FALSE.
     */
    public function __construct(ContainerInterface $container, $processingStopped = false)
    {
        $this->container = $container;
        $this->processingStopped = $processingStopped;
    }

    /**
     * Get resulting container.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Check if compilation chain should be stopped.
     *
     * @return bool|false
     */
    public function isProcessingStopped()
    {
        return $this->processingStopped;
    }
}