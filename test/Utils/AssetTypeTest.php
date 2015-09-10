<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Tests\Utils;

use RunOpenCode\AssetsInjection\Utils\AssetType;

class AssetTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExtension()
    {
        $this->assertSame('js', AssetType::guessExtension('//code.jquery.com/jquery-1.11.3.js'));
        $this->assertSame('js', AssetType::guessExtension(realpath(__DIR__ . '/../Data/web/js/jquery.js')));
        $this->assertNull(AssetType::guessExtension(realpath(__DIR__ . '/../Data/web/js/jquery')));
        $this->assertNull(AssetType::guessExtension('/some/path.with.dot/jquery'));
    }

    public function getAssetType()
    {
        $this->assertSame(AssetType::STYLESHEET, AssetType::guessAssetType('//code.jquery.com/jquery-1.11.3.css'));
        $this->assertSame(AssetType::JAVASCRIPT, AssetType::guessExtension(realpath(__DIR__ . '/../Data/web/js/jquery.js')));
        $this->assertNull(AssetType::guessExtension('/some/path.with.dot/jquery.unknown'));
    }

    public function testRegisterExtensionType()
    {
        AssetType::registerAssetType('new', AssetType::STYLESHEET);
        $this->assertSame(AssetType::STYLESHEET, AssetType::guessAssetType('//code.jquery.com/jquery-1.11.3.new'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExtensionTypeCanNotBeOverwritten()
    {
        AssetType::registerAssetType('new', AssetType::STYLESHEET);
        AssetType::registerAssetType('new', AssetType::JAVASCRIPT);
    }
}