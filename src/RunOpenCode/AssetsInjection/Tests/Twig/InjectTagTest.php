<?php

namespace RunOpenCode\AssetsInjection\Tests\Twig;

use RunOpenCode\AssetsInjection\Bridge\Twig\AssetsInjectionExtension;
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
        $this->environment = new \Twig_Environment($this->loader = new \Twig_Loader_Array(array()), array(
            'cache' => realpath(__DIR__ . '/../Data/tmp')
        ));

        $container = new Container(new LibraryCollection([
            new LibraryDefinition('jquery', array(
                new FileResource(realpath(__DIR__ . '/../Data/web/js/jquery.js')),
            )),
            new LibraryDefinition('my-lib', array(
                new ReferenceResource('jquery'),
                new HttpResource('http://code.jquery.com/jquery-1.11.3.js'),
            ))
        ]));

        $manager = new Manager($container, new SequentialRenderer(), array(
            'web_root' => realpath(__DIR__ . '/../Data/web'),
            'http_root' => 'http://www.mysite.com/'
        ));

        $this->environment->addExtension(new AssetsInjectionExtension($manager));
    }

    public function testInjectTag()
    {
        $this->loader->setTemplate('test', '{% set marko = "asda" %}{% inject janko, "pero", "djuro" %}');
        $this->environment->render('test', array('janko' => 'jquery'));
    }



}