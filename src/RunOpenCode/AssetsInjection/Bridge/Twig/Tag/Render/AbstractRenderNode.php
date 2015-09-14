<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render;

use RunOpenCode\AssetsInjection\Bridge\Twig\AssetsInjectionExtension;
use Twig_Compiler;
use Twig_Node;

/**
 * Class AbstractRenderNode
 *
 * Compiles asset inject render node.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render
 */
abstract class AbstractRenderNode extends Twig_Node
{

    /**
     * {@inheritdoc}
     */
    public function __construct(Twig_Node $position = null, Twig_Node $options = null, $lineno = 0, $tag = null)
    {
        parent::__construct([], array('position' => $position, 'options' => $options), $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('echo $this->env->getExtension(\'%s\')->getManager()->render(\'%s\', ', AssetsInjectionExtension::NAME, $this->getAssetType()))
            ->subcompile($this->getAttribute('position'))
            ->write(', ')
            ->subcompile($this->getAttribute('options'))
            ->write(');')
            ->write("\n");
    }

    /**
     * Get asset type which is subject of rendering.
     *
     * @return string
     */
    protected abstract function getAssetType();

}