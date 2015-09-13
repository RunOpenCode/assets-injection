<?php

namespace RunOpenCode\AssetsInjection\Tests\Factory\Loader;

use RunOpenCode\AssetsInjection\Factory\Loader\YamlLoader;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;

class YamlLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadValidConfiguration()
    {
        $loader = new YamlLoader(['assets_valid.yml']);

        $library = new LibraryCollection(
            $loader->load([
                realpath(__DIR__ . '/../../Data/config')
            ])
        );

        $this->assertTrue($library->hasDefinition('my-lib'));
        $this->assertTrue($library->hasDefinition('jquery'));

        $this->assertTrue($library->hasDefinition('jquery-ui'));
        $this->assertInstanceOf(ReferenceResource::class, $library->getDefinition('jquery-ui')[0]);

        $this->assertTrue($library->hasDefinition('glob-lib'));
        $this->assertEquals(1, $library->getDefinition('glob-lib')->count());
        $this->assertInstanceOf(GlobResource::class, $library->getDefinition('glob-lib')[0]);
    }
}