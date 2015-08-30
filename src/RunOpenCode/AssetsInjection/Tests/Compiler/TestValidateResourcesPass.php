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

use RunOpenCode\AssetsInjection\Compiler\ValidateResourcesPass;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Resource\StringResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class TestValidateResourcesPass extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidateResourcesPass
     */
    protected $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new ValidateResourcesPass();
    }

    public function testValidateResources()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js'))
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*')
            )),
            new LibraryDefinition('mylib', array(
                new ReferenceResource('tipsy'),
                new FileResource(realpath(__DIR__ . '/../Data/web/js/myjavascript.js'), array('filters' => ['test', 'filter'])),
                new StringResource('some code here...')
            ))
        )));

        $result = $this->compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\ConfigurationException
     */
    public function testValidateResourceFiltersShouldBeArray()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js'), array('filters' => 'string'))
            ))
        )));

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\ConfigurationException
     */
    public function testValidateResourceFiltersCanNotBeAssignedToReference()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js'))
            )),
            new LibraryDefinition('jquery-ui', array(
                new ReferenceResource('jquery', array('filters' => ['test', 'filter']))
            ))
        )));

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\ConfigurationException
     */
    public function testValidateResourceFiltersMustBeUnique()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js'), array('filters' => ['test', '?test', 'filter-x', 'filter-x', 'filter-y']))
            ))
        )));

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\UnavailableResourceException
     */
    public function testReferenceToNonExistingDefinition()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js'))
            )),
            new LibraryDefinition('jquery-ui', array(
                new ReferenceResource('jquery'),
                new ReferenceResource('non-existing-lib')
            ))
        )));

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\UnavailableResourceException
     */
    public function testReferenceToNonExistingFileResource()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('test', array(
                new FileResource('/path/to/non/existing/file')
            ))
        )));

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testReferenceToNonExistingGlobResource()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('test', array(
                new GlobResource('/path/to/non/existing/glob/files/*.js')
            ))
        )));

        $this->compilerPass->process($container);
    }


    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testReferenceToEmptyGlobResource()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('test', array(
                new GlobResource(realpath(__DIR__ . '/../Data/web/empty') . '/*')
            ))
        )));

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\UnavailableResourceException
     */
    public function testReferenceToNonExistingUrl()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('test', array(
                new HttpResource('http://this-url-does-not-exist.com')
            ))
        )));

        $this->compilerPass->process($container);
    }
}