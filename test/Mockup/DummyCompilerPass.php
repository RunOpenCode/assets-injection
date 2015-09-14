<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Tests\Mockup;

use RunOpenCode\AssetsInjection\Contract\Compiler\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class DummyCompilerPass
 *
 * Dummy compiler pass which will add definition with given name to container in order to check validity of executed test.
 *
 * @package RunOpenCode\AssetsInjection\Tests\Mockup
 */
final class DummyCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $definitionTestNameMarker;

    /**
     * @var bool
     */
    private $stopProcessing;

    /**
     * A constructor.
     *
     * @param string $definitionTestNameMarker      LibraryDefinition marker name to add to container.
     * @param bool|false $stopProcessing            Should compilation be stopped.
     */
    public function __construct($definitionTestNameMarker, $stopProcessing = false)
    {
        $this->definitionTestNameMarker = $definitionTestNameMarker;
        $this->stopProcessing = $stopProcessing;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        $container->getLibraries()->addDefinition(new LibraryDefinition($this->definitionTestNameMarker));
        return new CompilerPassResult($container, $this->stopProcessing);
    }
}