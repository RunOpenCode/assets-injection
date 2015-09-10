<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Tests;


use RunOpenCode\AssetsInjection\Library\FrozenLibraryCollection;
use RunOpenCode\AssetsInjection\Library\FrozenLibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;

class FrozenDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryCollectionAddDefinition()
    {
        $collection = new FrozenLibraryCollection();
        $collection->addDefinition(new FrozenLibraryDefinition('test'));
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryCollectionAddDefinitions()
    {
        $collection = new FrozenLibraryCollection();
        $collection->addDefinitions(array(
            new FrozenLibraryDefinition('test')
        ));
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryCollectionRemoveDefinition()
    {
        $collection = new FrozenLibraryCollection(array(
            new FrozenLibraryDefinition('test')
        ));
        $collection->removeDefinition('test');
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryCollectionRemoveDefinitions()
    {
        $collection = new FrozenLibraryCollection(array(
            new FrozenLibraryDefinition('test')
        ));
        $collection->removeDefinitions();
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryCollectionOffsetSet()
    {
        $collection = new FrozenLibraryCollection(array(
            new FrozenLibraryDefinition('test')
        ));
        $collection[] = new FrozenLibraryDefinition('another');
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryCollectionOffsetUnset()
    {
        $collection = new FrozenLibraryCollection(array(
            new FrozenLibraryDefinition('test')
        ));
        unset($collection['test']);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryDefinitionSetResources()
    {
        $definition = new FrozenLibraryDefinition('definition');
        $definition->setResources(array(
            new FileResource('/path/to/some/file')
        ));
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryDefinitionAddResource()
    {
        $definition = new FrozenLibraryDefinition('definition');
        $definition->addResource(new FileResource('/path/to/some/file'));
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryDefinitionReplaceResource()
    {
        $definition = new FrozenLibraryDefinition('definition', array(
            $resource = new FileResource('/path/to/some/file')
        ));
        $definition->replaceResource($resource, [new FileResource('/path/to/some/file')]);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryDefinitionRemoveResource()
    {
        $definition = new FrozenLibraryDefinition('definition', array(
            $resource = new FileResource('/path/to/some/file')
        ));
        $definition->removeResource($resource);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryDefinitionOffsetSet()
    {
        $definition = new FrozenLibraryDefinition('definition');
        $definition[] = new FileResource('/path/to/some/file');
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testFrozenLibraryDefinitionOffsetUnset()
    {
        $definition = new FrozenLibraryDefinition('definition', array(
            $resource = new FileResource('/path/to/some/file')
        ));
        unset($definition[0]);
    }
}