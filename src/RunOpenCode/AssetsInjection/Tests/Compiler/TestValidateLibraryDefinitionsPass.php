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

use RunOpenCode\AssetsInjection\Compiler\ValidateLibraryDefinitionsPass;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;


class TestValidateLibraryDefinitionsPass extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidateLibraryDefinitionsPass
     */
    protected $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new ValidateLibraryDefinitionsPass();
    }

    public function testValidateResourceCountValid()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new HttpResource('https://code.jquery.com/jquery-2.1.4.min.js')
            ))
        )));

        $result = $this->compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testValidateResourceCountInvalid()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array())
        )));

        $result = $this->compilerPass->process($container);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testInvalidDefinitionName()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('this invalid name', array(
                new HttpResource('https://code.jquery.com/jquery-2.1.4.min.js')
            ))
        )));

        $result = $this->compilerPass->process($container);
    }
}