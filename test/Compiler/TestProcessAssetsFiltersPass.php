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

use Assetic\FilterManager;
use RunOpenCode\AssetsInjection\Compiler\ProcessAssetsFiltersPass;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Tests\Mockup\DummyFilter;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * @codeCoverageIgnore
 */
class TestProcessAssetsFiltersPass extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $outputDir;

    public function setUp()
    {
        $this->outputDir = realpath(__DIR__ . '/../Data/web/output');
        $this->emptyOutput();
    }

    public function tearDown()
    {
        $this->emptyOutput();
    }

    public function testProcessAssetWithoutFiltering()
    {
        $this->emptyOutput();
        $sourceFile = realpath(__DIR__ . '/../Data/web/js/myjavascript.js');

        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('mylib', array(
                new FileResource($sourceFile)
            ))
        )));

        $result = $this->getCompilerPass(false)->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());

        $files = glob(rtrim($this->outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '/*');

        $this->assertSame(0, count($files), 'File should not be processed.');
    }

    public function testProcessAssetWithFiltering()
    {
        $this->emptyOutput();
        $sourceFile = realpath(__DIR__ . '/../Data/web/js/myjavascript.js');

        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('mylib', array(
                new FileResource($sourceFile, array('filters' => array('mockup')))
            ))
        )));

        $result = $this->getCompilerPass(false)->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());

        $files = glob(rtrim($this->outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '/*');

        $this->assertSame(1, count($files), 'Only one file should be dumped into output dir.');
        $this->assertNotEquals(file_get_contents($sourceFile), file_get_contents($files[0]), 'Source and dumped files should not be same.');
        $this->assertNotSame(false, strpos(file_get_contents($files[0]), file_get_contents($sourceFile)));
        $this->assertNotSame(false, strpos(file_get_contents($files[0]), 'Provided content has been filtered successfully by "\\RunOpenCode\\AssetsInjection\\Tests\\Mockup\\DummyFilter":'), 'Dumped file should have filter mark.');

        $this->assertSame(AssetType::guessExtension($sourceFile), AssetType::guessExtension($files[0]), 'File has proper extension.');

        $this->assertFalse(array_key_exists('filters', $result->getContainer()->getLibraries()->getDefinition('mylib')->getResources()[0]->getOptions()));
    }

    public function testProcessAssetWithIncludedOptionalFilter()
    {
        $this->emptyOutput();
        $sourceFile = realpath(__DIR__ . '/../Data/web/js/myjavascript.js');

        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('mylib', array(
                new FileResource($sourceFile, array('filters' => array('?mockup')))
            ))
        )));

        $result = $this->getCompilerPass(false)->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());

        $files = glob(rtrim($this->outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '/*');

        $this->assertSame(1, count($files), 'Only one file should be dumped into output dir.');
        $this->assertNotEquals(file_get_contents($sourceFile), file_get_contents($files[0]), 'Source and dumped files should not be same.');
        $this->assertNotSame(false, strpos(file_get_contents($files[0]), file_get_contents($sourceFile)));
        $this->assertNotSame(false, strpos(file_get_contents($files[0]), 'Provided content has been filtered successfully by "\\RunOpenCode\\AssetsInjection\\Tests\\Mockup\\DummyFilter":'), 'Dumped file should have filter mark.');
        $this->assertSame('.prod.', substr($files[0], -8, 6), 'File name should contain ".prod.".');
        $this->assertSame(AssetType::guessExtension($sourceFile), AssetType::guessExtension($files[0]), 'File has proper extension.');

        $this->assertFalse(array_key_exists('filters', $result->getContainer()->getLibraries()->getDefinition('mylib')->getResources()[0]->getOptions()));
    }

    public function testProcessAssetWithExcludedOptionalFilter()
    {
        $this->emptyOutput();
        $sourceFile = realpath(__DIR__ . '/../Data/web/js/myjavascript.js');

        $container = new Container(new LibraryCollection(array(
            new LibraryDefinition('mylib', array(
                new FileResource($sourceFile, array('filters' => array('?mockup')))
            ))
        )));

        $result = $this->getCompilerPass(true)->process($container);
        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());

        $files = glob(rtrim($this->outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '/*');

        $this->assertSame(1, count($files), 'Only one file should be dumped into output dir.');
        $this->assertSame(file_get_contents($sourceFile), file_get_contents($files[0]), 'Source and dumped files should be same.');
        $this->assertSame('.dev.', substr($files[0], -7, 5), 'File name should contain ".dev.".');
        $this->assertSame(AssetType::guessExtension($sourceFile), AssetType::guessExtension($files[0]), 'File should have proper extension.');

        $this->assertFalse(array_key_exists('filters', $result->getContainer()->getLibraries()->getDefinition('mylib')->getResources()[0]->getOptions()));
    }

    private function emptyOutput()
    {
        chmod($this->outputDir, 0777);
        array_map('unlink', glob(rtrim($this->outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '/*', GLOB_BRACE));
    }

    private function getCompilerPass($development = false)
    {
        $filterManager = new FilterManager();
        $filterManager->set('mockup', new DummyFilter());

        return new ProcessAssetsFiltersPass($filterManager, array(
            'development' => $development,
            'output_dir' => $this->outputDir
        ));
    }
}