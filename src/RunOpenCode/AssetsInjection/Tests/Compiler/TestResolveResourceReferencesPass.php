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

use RunOpenCode\AssetsInjection\Compiler\ResolveResourceReferencesPass;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class TestResolveResourceReferencesPass extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResolveResourceReferencesPass
     */
    protected $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new ResolveResourceReferencesPass();
    }

    public function testResourceReplacement()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery-ui'),
                new FileResource('/my-lib/path/to/my/file/1.js'),
                new FileResource('/my-lib/path/to/my/file/2.js'),
                new FileResource('/my-lib/path/to/my/file/3.js')
            )),
            new LibraryDefinition('jquery', array(
                new FileResource('/jquery/path/to/file/jquery.js')
            )),
            new LibraryDefinition('jquery-ui', array(
                new ReferenceResource('jquery'),
                new FileResource('/jquery-ui/path/to/file/jquery-ui.js'),
                new FileResource('/jquery-ui/path/to/file/jquery-ui.css')
            )),
            new LibraryDefinition('my-other-lib/in-some-namespace', array(
                new ReferenceResource('jquery-ui'),
                new ReferenceResource('jquery'),
                new FileResource('/my-other-lib/in-some-namespace/path/to/my/other/file/javascript.js'),
                new FileResource('/my-other-lib/in-some-namespace/path/to/my/other/file/stylesheet.css')
            )),
            new LibraryDefinition('namespace-test', array(
                new ReferenceResource('my-other-lib/in-some-namespace'),
                new FileResource('/namespace-test/path/to/my/other/file/javascript.js'),
                new FileResource('/namespace-test/path/to/my/other/file/stylesheet.css')
            )),
        )));

        $result = $this->compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());

        /**
         * @var LibraryDefinitionInterface $definition
         */
        $definition = $result->getContainer()->getLibraries()->getDefinition('namespace-test');

        $files = [];

        /**
         * @var ResourceInterface $resource
         */
        foreach ($definition->getResources() as $resource) {
            $files[] = $resource->getSource();
        }

        $this->assertSame(array(
            '/jquery/path/to/file/jquery.js',
            '/jquery-ui/path/to/file/jquery-ui.js',
            '/jquery-ui/path/to/file/jquery-ui.css',
            '/my-other-lib/in-some-namespace/path/to/my/other/file/javascript.js',
            '/my-other-lib/in-some-namespace/path/to/my/other/file/stylesheet.css',
            '/namespace-test/path/to/my/other/file/javascript.js',
            '/namespace-test/path/to/my/other/file/stylesheet.css',
        ), $files);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\CircularReferenceException
     */
    public function testCircularReferenceDetected()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('lib1', array(
                new ReferenceResource('lib2'),
            )),
            new LibraryDefinition('lib2', array(
                new ReferenceResource('lib3')
            )),
            new LibraryDefinition('lib3', array(
                new ReferenceResource('lib1')
            ))
        )));

        $result = $this->compilerPass->process($container);
    }

}