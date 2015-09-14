<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Tests\Integration;

use RunOpenCode\AssetsInjection\Compiler;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\DumperInterface;
use RunOpenCode\AssetsInjection\Dumper\PhpDumper;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Manager;
use RunOpenCode\AssetsInjection\Renderer\SequentialRenderer;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;

class TestDynamicAssetsInjection extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var DumperInterface
     */
    protected $dumper;

    public function setUp()
    {
        $container = new Container(new LibraryCollection([
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
            )),
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery'),
                new GlobResource(realpath(__DIR__ . '/../Data/web/lib/tipsy') . '/*'),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
            ))
        ]));

        $compiler = new Compiler();

        $compiler
            ->addCompilerPass(new Compiler\CheckCircularReferencesPass(), 0)
            ->addCompilerPass(new Compiler\ValidateLibraryDefinitionsPass(), 1)
            ->addCompilerPass(new Compiler\ValidateResourcesPass(), 2)
            ->addCompilerPass(new Compiler\ProcessGlobResourcesPass(), 3)
            ->addCompilerPass(new Compiler\IncludeRemoteResourcesDynamicallyPass(), 4)
            ->addCompilerPass(new Compiler\ResolveResourceReferencesPass(), 5)
            ->addCompilerPass(new Compiler\CheckContainerResourcesRenderability(), 6)
        ;

        $this->container = $compiler->compile($container)->getContainer();

        $this->dumper = new PhpDumper();
    }

    public function testDynamicContainerWithSequentialRenderer()
    {

        file_put_contents(
            $filename = sprintf('%s/%s',realpath(__DIR__ . '/../Data/tmp'), 'testDynamicContainerWithSequentialRenderer.php'),
            $this->dumper->dump($this->container, [
                'type' => PhpDumper::DYNAMIC_CONTAINER,
                'classname' => 'testDynamicContainerWithSequentialRenderer'
            ])
        );

        require_once($filename);


        $container = new \testDynamicContainerWithSequentialRenderer();

        $this->assertNotSame($this->container, $container);
        $this->assertInstanceOf(ContainerInterface::class, $container);

        $renderer = new Manager($container, new SequentialRenderer(), array(
            'web_root' => realpath(__DIR__ . '/../Data/web'),
            'http_root' => 'http://www.mysite.com/'
        ));

        $container->inject('my-lib');

//        echo $renderer->render('js');
    }
}