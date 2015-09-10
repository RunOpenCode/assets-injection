<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Tests\Dumper;

use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Contract\DumperInterface;
use RunOpenCode\AssetsInjection\Dumper\PhpDumper;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\JavascriptStringResource;

class TestPhpDumper extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DumperInterface
     */
    protected $dumper;

    public function setUp()
    {
        $this->dumper = new PhpDumper();
    }

    public function testDumpContainer()
    {
        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js'))
            )),
            new LibraryDefinition('my-lib', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new FileResource(realpath(__DIR__ . '/../Data/web/js/myjavascript.js'))
            )),
            new LibraryDefinition('my-other-lib', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new FileResource(realpath(__DIR__ . '/../Data/web/js/myjavascript.js')),
                new HttpResource('//code.jquery.com/jquery-1.11.3.js')
            )),
            new LibraryDefinition('my-third-lib', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
                new FileResource(realpath(__DIR__ . '/../Data/web/js/myjavascript.js')),
                new HttpResource('//code.jquery.com/jquery-1.11.3.js'),
                new JavascriptStringResource(sprintf(';(function(r){ r.DynamicAssetInclusion.loadJavascript(\'http://code.jquery.com/jquery-1.11.3.js\'); })(RunOpenCode);'))
            )),
        )));

        //file_put_contents('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/output/test.php', $this->dumper->dump($container)) ;
    }
}