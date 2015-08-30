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

use RunOpenCode\AssetsInjection\Compiler\CheckCircularReferencesPass;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Contract\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class TestCheckCircularReferencesPass extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompilerPassInterface
     */
    protected $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new CheckCircularReferencesPass();
    }

    public function testCircularReferenceNotDetected()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery-ui'),
                new FileResource('/path/to/my/file/javascript.js')
            )),
            new LibraryDefinition('jquery', array(
                new FileResource('/path/to/file/jquery.js')
            )),
            new LibraryDefinition('jquery-ui', array(
                new FileResource('/path/to/file/jquery-ui.js'),
                new FileResource('/path/to/file/jquery-ui.css')
            )),
            new LibraryDefinition('my-other-lib', array(
                new ReferenceResource('jquery-ui'),
                new ReferenceResource('jquery'),
                new ReferenceResource('my-lib'),
                new FileResource('/path/to/my/other/file/javascript.js'),
                new FileResource('/path/to/my/other/file/stylesheet.css')
            )),
            new LibraryDefinition('namespace-test', array(
                new ReferenceResource('my-other-lib'),
                new ReferenceResource('my-lib'),
                new FileResource('/path/to/my/other/file/javascript.js'),
                new FileResource('/path/to/my/other/file/stylesheet.css')
            )),
        )));
        $result = $this->compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\CircularReferenceException
     */
    public function testCircularReferenceDetected()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery-ui'),
                new ReferenceResource('namespace-test'),
                new FileResource('/path/to/my/file/javascript.js')
            )),
            new LibraryDefinition('jquery', array(
                new FileResource('/path/to/file/jquery.js')
            )),
            new LibraryDefinition('jquery-ui', array(
                new FileResource('/path/to/file/jquery-ui.js'),
                new FileResource('/path/to/file/jquery-ui.css')
            )),
            new LibraryDefinition('my-other-lib/in-some-namespace', array(
                new ReferenceResource('jquery-ui'),
                new ReferenceResource('jquery'),
                new ReferenceResource('my-lib'),
                new FileResource('/path/to/my/other/file/javascript.js'),
                new FileResource('/path/to/my/other/file/stylesheet.css')
            )),
            new LibraryDefinition('namespace-test', array(
                new ReferenceResource('my-other-lib/in-some-namespace'),
                new FileResource('/path/to/my/other/file/javascript.js'),
                new FileResource('/path/to/my/other/file/stylesheet.css')
            )),
        )));

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\CircularReferenceException
     * @expectedExceptionMessage Circular reference detected at definition "my-lib", dependency chain (path: [my-lib] => [namespace-test] => [my-other-lib/in-some-namespace] => [my-lib]).
     */
    public function testCircularReferenceDetectedWithAliases()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery-ui'),
                new ReferenceResource('namespace-test'),
                new FileResource('/path/to/my/file/javascript.js', array('ref' => 'aliased-js'))
            )),
            new LibraryDefinition('jquery', array(
                new FileResource('/path/to/file/jquery.js')
            )),
            new LibraryDefinition('jquery-ui', array(
                new FileResource('/path/to/file/jquery-ui.js'),
                new FileResource('/path/to/file/jquery-ui.css')
            )),
            new LibraryDefinition('my-other-lib/in-some-namespace', array(
                new ReferenceResource('jquery-ui'),
                new ReferenceResource('jquery'),
                new ReferenceResource('my-lib'),
                new FileResource('/path/to/my/other/file/javascript.js'),
                new FileResource('/path/to/my/other/file/stylesheet.css')
            )),
            new LibraryDefinition('namespace-test', array(
                new ReferenceResource('my-other-lib/in-some-namespace'),
                new FileResource('/path/to/my/other/file/javascript.js'),
                new FileResource('/path/to/my/other/file/stylesheet.css')
            )),
        )));

        $this->compilerPass->process($container);
    }

}