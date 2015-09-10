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

use RunOpenCode\AssetsInjection\Compiler\CompilerPassCollection;
use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;
use RunOpenCode\AssetsInjection\Tests\Mockup\DummyCompilerPass;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

class TestCompilerPassCollection extends \PHPUnit_Framework_TestCase
{

    public function testOrderOfCompilerPasses()
    {
        $container = new Container(new LibraryCollection());

        $collection = new CompilerPassCollection();

        $collection->addCompiler(new DummyCompilerPass('definition-x'), 100);
        $collection->addCompiler(new DummyCompilerPass('definition-y'), 500);
        $collection->addCompiler(new DummyCompilerPass('definition-z'), -500);

        $result = $collection->process($container);

        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertFalse($result->isProcessingStopped());

        $definitions = [];

        /**
         * @var LibraryDefinitionInterface $definition
         */
        foreach ($result->getContainer()->getLibraries()->getDefinitions() as $definition) {
            $definitions[] = $definition->getName();
        }

        $this->assertSame(array(
            'definition-z',
            'definition-x',
            'definition-y'
        ), $definitions);
    }

    public function testStopProcessing()
    {
        $container = new Container(new LibraryCollection());

        $collection = new CompilerPassCollection();

        $collection->addCompiler(new DummyCompilerPass('definition-x', true), 100);
        $collection->addCompiler(new DummyCompilerPass('definition-y'), 500);
        $collection->addCompiler(new DummyCompilerPass('definition-z'), -500);

        $result = $collection->process($container);

        $this->assertInstanceOf(CompilerPassResult::class, $result);
        $this->assertTrue($result->isProcessingStopped());

        $definitions = [];

        /**
         * @var LibraryDefinitionInterface $definition
         */
        foreach ($result->getContainer()->getLibraries()->getDefinitions() as $definition) {
            $definitions[] = $definition->getName();
        }

        $this->assertSame(array(
            'definition-z',
            'definition-x'
        ), $definitions);
    }

    public function testCompilerPassIteratorAndCountable()
    {
        $collection = new CompilerPassCollection();

        $collection->addCompiler($compiler_x = new DummyCompilerPass('definition-x', true), 100);
        $collection->addCompiler($compiler_y = new DummyCompilerPass('definition-y'), 500);
        $collection->addCompiler($compiler_z = new DummyCompilerPass('definition-z'), -500);
        $collection->addCompiler($compiler_t = new DummyCompilerPass('definition-t'), 500);
        $collection->addCompiler($compiler_w = new DummyCompilerPass('definition-w'), 500);

        $this->assertSame(5, count($collection));

        $iterationResult = [];

        foreach ($collection as $compiler) {
            $iterationResult[] = $compiler;
        }

        $this->assertSame(array(
            $compiler_z,
            $compiler_x,
            $compiler_y,
            $compiler_t,
            $compiler_w
        ), $iterationResult);
    }

}