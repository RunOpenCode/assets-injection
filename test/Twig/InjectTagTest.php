<?php

namespace RunOpenCode\AssetsInjection\Tests\Twig;

use RunOpenCode\AssetsInjection\Bridge\Twig\AssetsInjectionExtension;
use RunOpenCode\AssetsInjection\Compiler;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Manager;
use RunOpenCode\AssetsInjection\Renderer\SequentialRenderer;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;

class InjectTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var \Twig_Loader_Array
     */
    protected $loader;

    public function setUp()
    {
        $this->loader = new \Twig_Loader_Filesystem(array(
            realpath(__DIR__ . '/../Data/twig')
        ));

        $this->environment = new \Twig_Environment($this->loader, array());

        $container = new Container(new LibraryCollection([
            new LibraryDefinition('jquery', array(
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js')
            )),
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery'),
                new FileResource(realpath(__DIR__ . '/../Data/web/js/myjavascript.js'))
            )),
            new LibraryDefinition('tipsy', array(
                new ReferenceResource('jquery'),
                new FileResource(realpath(__DIR__ . '/../Data/web/lib/tipsy/tipsy.js')),
                new FileResource(realpath(__DIR__ . '/../Data/web/lib/tipsy/tipsy.css')),
            ))
        ]));

        $compiler = new Compiler();

        $compiler
            ->addCompilerPass(new Compiler\CheckCircularReferencesPass(), 0)
            ->addCompilerPass(new Compiler\ValidateLibraryDefinitionsPass(), 1)
            ->addCompilerPass(new Compiler\ValidateResourcesPass(), 2)
            ->addCompilerPass(new Compiler\ProcessGlobResourcesPass(), 3)
            ->addCompilerPass(new Compiler\ResolveResourceReferencesPass(), 5)
            ->addCompilerPass(new Compiler\CheckContainerResourcesRenderability(), 6);

        $manager = new Manager($compiler->compile($container)->getContainer(), new SequentialRenderer(), array(
            'web_root' => realpath(__DIR__ . '/../Data/web'),
            'http_root' => 'http://www.mysite.com/'
        ));

        $this->environment->addExtension(new AssetsInjectionExtension($manager, ['bufferize' => true]));
    }

    public function testInjectTag()
    {
        $html = $this->environment->render('page.html.twig');

        $this->assertContains('/lib/tipsy/tipsy.css', $html);
        $this->assertContains('/lib/tipsy/tipsy.js', $html);
        $this->assertContains('/js/myjavascript.js', $html);
        $this->assertContains('http://code.jquery.com/jquery-1.11.3.js', $html);
    }

}