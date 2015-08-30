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

use RunOpenCode\AssetsInjection\Compiler\CheckContainerResourcesRenderability;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Resource\StringResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class TestCheckContainerResourcesRenderability extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CheckContainerResourcesRenderability
     */
    protected $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new CheckContainerResourcesRenderability();
    }

    public function testCheckValidRenderability()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js'))
            ))
        )));

        $result = $this->compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\RuntimeException
     */
    public function testInvalidRenderabilityBecauseOfReference()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
            )),
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery')
            ))
        )));

        $result = $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\RuntimeException
     */
    public function testInvalidRenderabilityBecauseOfGlob()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
            )),
            new LibraryDefinition('my-lib', array(
                new GlobResource('/glob/path/*')
            ))
        )));

        $result = $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\RuntimeException
     */
    public function testInvalidRenderabilityBecauseOfUndeterminedSourceType()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
            )),
            new LibraryDefinition('my-lib', array(
                new StringResource('some code...')
            ))
        )));

        $result = $this->compilerPass->process($container);
    }

}