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

use RunOpenCode\AssetsInjection\Contract\LibraryCollectionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LibraryCollectionInterface
     */
    protected $manager;

    public function setup()
    {
        $this->manager = new LibraryCollection([
            new LibraryDefinition('test', [
                new FileResource('/some/path/to/file')
            ])
        ]);
    }

    public function testLibraryCollectionConstructor()
    {
        $collection = new LibraryCollection([
            new LibraryDefinition('test', [
                new FileResource('/some/path/to/file')
            ])
        ]);
        $this->assertTrue($collection->hasDefinition('test'));
        $this->assertEquals(1, count($collection));
    }

    public function testRemoveDefinitions()
    {
        $collection = new LibraryCollection([
            new LibraryDefinition('test', [
                new FileResource('/some/path/to/file')
            ])
        ]);

        $this->assertTrue($collection->hasDefinition('test'));
        $this->assertEquals(1, count($collection));

        $collection->removeDefinitions();

        $this->assertSame(0, count($collection));
    }

    public function testManipulateDefinitions()
    {
        $collection = new LibraryCollection([
            new LibraryDefinition('test', [
                new FileResource('/some/path/to/file')
            ])
        ]);

        $this->assertTrue($collection->hasDefinition('test'));
        $this->assertEquals(1, count($collection));

        $collection->addDefinition(new LibraryDefinition('first', [
            new FileResource('/path/to/a/file')
        ]));

        $this->assertTrue($collection->hasDefinition('first'));
        $this->assertEquals(2, count($collection));

        $collection->removeDefinition('first');
        $this->assertFalse($collection->hasDefinition('first'));
        $this->assertSame(1, count($collection));

        $collection->addDefinitions([
            new LibraryDefinition('first', [
                new FileResource('/some/path/to/file')
            ]),
            new LibraryDefinition('second', [
                new FileResource('/path/to/another/file')
            ])
        ]);
        $this->assertTrue($collection->hasDefinition('first'));
        $this->assertTrue($collection->hasDefinition('second'));
        $this->assertEquals(3, count($collection));

        $collection->removeDefinitions();
        $this->assertEquals(0, count($collection));
    }

    public function testGetExistingDefinition()
    {
        $collection = new LibraryCollection();

        $collection->addDefinitions([
            new LibraryDefinition('first', [
                new FileResource('/some/path/to/file')
            ]),
            new LibraryDefinition('second', [
                new FileResource('/path/to/another/file')
            ])
        ]);

        $this->assertSame('first', $collection->getDefinition('first')->getName());
        $this->assertSame('second', $collection->getDefinition('second')->getName());
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\LogicException
     */
    public function testGetNonExistingDefinition()
    {
        $collection = new LibraryCollection();

        $collection->addDefinitions([
            new LibraryDefinition('first', [
                new FileResource('/some/path/to/file')
            ]),
            new LibraryDefinition('second', [
                new FileResource('/path/to/another/file')
            ])
        ]);

        $collection->getDefinition('some-none-existing-definition');
    }

    public function testIteratingTroughDefinitionsAndResources()
    {
        $collection = new LibraryCollection([
            new LibraryDefinition('first', [
                new FileResource('/some/path/to/file/1'),
                new FileResource('/some/path/to/file/2')
            ]),
            new LibraryDefinition('second', [
                new FileResource('/path/to/another/file/1'),
                new FileResource('/path/to/another/file/2')
            ])
        ]);

        $definitions = [];
        $sources = [];

        /**
         * @var LibraryDefinition $definition
         */
        foreach ($collection as $definition) {
            $definitions[] = $definition->getName();

            /**
             * @var ResourceInterface $resource
             */
            foreach ($definition as $resource) {
                $sources[] = $resource->getSource();
            }
        }

        $this->assertSame(array('first', 'second'), $definitions);
        $this->assertSame(array(
            '/some/path/to/file/1',
            '/some/path/to/file/2',
            '/path/to/another/file/1',
            '/path/to/another/file/2'
        ), $sources);
    }

    public function testRemoveResource()
    {
        $definition = new LibraryDefinition('first', [
            $resource1 = new FileResource('/some/path/to/file/1'),
            $resource2 = new FileResource('/some/path/to/file/2')
        ]);

        $this->assertEquals(2, count($definition));

        $definition->removeResource($resource1);

        $this->assertEquals(1, count($definition));

        $this->assertSame($resource2, $definition->getResources()[0]);
    }

    public function testReplaceResource()
    {
        $definition = new LibraryDefinition('first', [
            $resource1 = new FileResource('/some/path/to/file/1'),
            $resource2 = new FileResource('/some/path/to/file/2')
        ]);

        $this->assertEquals(2, count($definition));

        $definition->replaceResource($resource1, array(
            $resource3 = new FileResource('/some/path/to/file/3'),
            $resource4 = new FileResource('/some/path/to/file/4')
        ));

        $this->assertEquals(3, count($definition));

        $this->assertSame($resource3, $definition->getResources()[0]);
        $this->assertSame($resource4, $definition->getResources()[1]);
        $this->assertSame($resource2, $definition->getResources()[2]);
    }

    public function testArrayAccess()
    {
        $collection = new LibraryCollection([
            new LibraryDefinition('first', [
                new FileResource('/some/path/to/file/1'),
                new FileResource('/some/path/to/file/2')
            ]),
            new LibraryDefinition('second', [
                new FileResource('/path/to/another/file/1'),
                new FileResource('/path/to/another/file/2')
            ])
        ]);

        $this->assertTrue(isset($collection['first']));
        $this->assertEquals('first', $collection['first']->getName());

        unset($collection['first']);
        $this->assertFalse(isset($collection['first']));

        $collection[] = new LibraryDefinition('first', [
            new FileResource('/some/path/to/file/1'),
            new FileResource('/some/path/to/file/2')
        ]);
        $this->assertTrue(isset($collection['first']));
        $this->assertEquals('first', $collection['first']->getName());
        $this->assertEquals(2, $collection['first']->count());

        $collection[] = new LibraryDefinition('first');
        $this->assertEquals(0, $collection['first']->count());

        $collection['first'][] = new FileResource('/some/path/to/file/1');
        $collection['first'][] = new FileResource('/some/path/to/file/2');
        $this->assertEquals(2, $collection['first']->count());

        $this->assertEquals('/some/path/to/file/1', $collection['first'][0]->getSource());

        unset($collection['first'][0]);
        $this->assertEquals('/some/path/to/file/2', $collection['first'][0]->getSource());
        $this->assertFalse(isset($collection['first'][1]));
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testArrayAccessAddNonDefinitionItemToManager()
    {
        $collection = new LibraryCollection();
        $collection[] = new FileResource('/path/to/file');
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testArrayAccessProvideOffset()
    {
        $collection = new LibraryCollection();
        $collection['first'] = new LibraryDefinition('first');
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testArrayAccesAddNonResourceToDefinition()
    {
        $definition = new LibraryDefinition('first');
        $definition[] = new LibraryDefinition('second');
    }
}