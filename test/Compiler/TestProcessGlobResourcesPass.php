<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Tests\Compiler;

use RunOpenCode\AssetsInjection\Compiler\ProcessGlobResourcesPass;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Contract\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class TestProcessGlobResourcesPass extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompilerPassInterface
     */
    protected $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new ProcessGlobResourcesPass();
    }

    public function testGlobResourcePass()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
                new HttpResource('//code.jquery.com/jquery-1.11.3.js')
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*')
            ))
        )));

        $result = $this->compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());

        /**
         * @var LibraryDefinitionInterface $definition
         */
        $definition = $result->getContainer()->getLibraries()->getDefinition('tipsy');

        $files = [];

        /**
         * @var ResourceInterface $resource
         */
        foreach ($definition->getResources() as $resource) {
            $files[] = $resource->getSource();
        }

        $this->assertSame(array(
            'jquery',
            realpath(__DIR__ . '/../Data/web/lib/tipsy/tipsy.css'),
            realpath(__DIR__ . '/../Data/web/lib/tipsy/tipsy.js')
        ), $files);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidGlobResource()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
                new HttpResource('//code.jquery.com/jquery-1.11.3.js')
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/empty') . '/*')
            ))
        )));

        $this->compilerPass->process($container);
    }
}