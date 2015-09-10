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

use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Compiler\IncludeRemoteResourcesDynamicallyPass;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Resource\StringResource;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class TestIncludeRemoteResourcesDynamicallyPass extends \PHPUnit_Framework_TestCase
{
    public function testDynamicReplacement()
    {
        $container = new Container($manager = new LibraryCollection([
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
                new HttpResource('//code.jquery.com/jquery-1.11.3.js')
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*')
            ))
        ]));

        $compilerPass = new IncludeRemoteResourcesDynamicallyPass();

        $result = $compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
        $this->assertEquals(4, $manager['jquery']->count());

        $this->assertTrue($manager['jquery'][1] instanceof FileResource);
        $this->assertTrue(strpos($manager['jquery'][1]->getSource(), '/Public/js/RunOpenCode.DynamicAssetInclusion') !== false);

        $this->assertTrue($manager['jquery'][2] instanceof StringResource);
        $this->assertFalse($manager['jquery'][2]->getSource() == 'http://code.jquery.com/jquery-1.11.3.js');
        $this->assertTrue(strpos($manager['jquery'][2]->getSource(), 'http://code.jquery.com/jquery-1.11.3.js') !== false);
        $this->assertTrue(strpos($manager['jquery'][2]->getSource(), 'r.DynamicAssetInclusion.loadJavascript') !== false);

        $this->assertTrue($manager['jquery'][3] instanceof StringResource);
        $this->assertFalse($manager['jquery'][3]->getSource() == '//code.jquery.com/jquery-1.11.3.js');
        $this->assertTrue(strpos($manager['jquery'][3]->getSource(), '//code.jquery.com/jquery-1.11.3.js') !== false);
        $this->assertTrue(strpos($manager['jquery'][3]->getSource(), 'r.DynamicAssetInclusion.loadJavascript') !== false);
    }

    public function testBlackList()
    {
        $container = new Container($manager = new LibraryCollection([
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
                new HttpResource('//code.jquery.com/jquery-1.11.3.js')
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*')
            ))
        ]));

        $compilerPass = new IncludeRemoteResourcesDynamicallyPass(array(
            'blacklist' => [
                'http://code.jquery.com/jquery-1.11.3.js'
            ]
        ));

        $result = $compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
        $this->assertEquals(4, $manager['jquery']->count());

        $this->assertTrue($manager['jquery'][1]->getSource() == 'http://code.jquery.com/jquery-1.11.3.js');

        $this->assertTrue(strpos($manager['jquery'][2]->getSource(), '/Public/js/RunOpenCode.DynamicAssetInclusion') !== false);

        $this->assertTrue($manager['jquery'][3] instanceof StringResource);
        $this->assertFalse($manager['jquery'][3]->getSource() == '//code.jquery.com/jquery-1.11.3.js');
        $this->assertTrue(strpos($manager['jquery'][3]->getSource(), '//code.jquery.com/jquery-1.11.3.js') !== false);
    }

    public function testWhiteList()
    {
        $container = new Container($manager = new LibraryCollection([
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
                new HttpResource('//code.jquery.com/jquery-1.11.3.js')
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*')
            ))
        ]));

        $compilerPass = new IncludeRemoteResourcesDynamicallyPass(array(
            'whitelist' => [
                'http://code.jquery.com/jquery-1.11.3.js'
            ]
        ));

        $result = $compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
        $this->assertEquals(4, $manager['jquery']->count());

        $this->assertTrue(strpos($manager['jquery'][1]->getSource(), '/Public/js/RunOpenCode.DynamicAssetInclusion') !== false);

        $this->assertTrue($manager['jquery'][2] instanceof StringResource);
        $this->assertFalse($manager['jquery'][2]->getSource() == 'http://code.jquery.com/jquery-1.11.3.js');
        $this->assertTrue(strpos($manager['jquery'][2]->getSource(), 'http://code.jquery.com/jquery-1.11.3.js') !== false);

        $this->assertTrue($manager['jquery'][3]->getSource() == '//code.jquery.com/jquery-1.11.3.js');
    }

    public function testIncludeOnlyProvidedType()
    {
        $container = new Container($manager = new LibraryCollection([
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
                new HttpResource('//code.jquery.com/jquery-1.11.3.css')
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*')
            ))
        ]));

        $compilerPass = new IncludeRemoteResourcesDynamicallyPass(array( 'include' => [ 'css' ] ));

        $result = $compilerPass->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());
        $this->assertEquals(4, $manager['jquery']->count());

        $this->assertTrue($manager['jquery'][1]->getSource() == 'http://code.jquery.com/jquery-1.11.3.js');

        $this->assertTrue(strpos($manager['jquery'][2]->getSource(), '/Public/js/RunOpenCode.DynamicAssetInclusion') !== false);

        $this->assertTrue($manager['jquery'][3] instanceof StringResource);
        $this->assertFalse($manager['jquery'][3]->getSource() == '//code.jquery.com/jquery-1.11.3.css');
        $this->assertTrue(strpos($manager['jquery'][3]->getSource(), '//code.jquery.com/jquery-1.11.3.css') !== false);
        $this->assertTrue(strpos($manager['jquery'][3]->getSource(), 'r.DynamicAssetInclusion.loadStylesheet') !== false);
    }

    /**
     * @expectedException \RunOpenCode\AssetsInjection\Exception\InvalidArgumentException
     */
    public function testWhiteListAndBlackListCanNotCoexist()
    {
        $compilerPass = new IncludeRemoteResourcesDynamicallyPass(array(
            'whitelist' => ['test'],
            'blacklist' => ['test'],
        ));
    }
}