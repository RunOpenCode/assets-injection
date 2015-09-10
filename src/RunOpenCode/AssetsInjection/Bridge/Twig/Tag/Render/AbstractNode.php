<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render;

use RunOpenCode\AssetsInjection\Bridge\Twig\AssetsInjectionExtension;
use Twig_Compiler;
use Twig_Node;

abstract class AbstractNode extends Twig_Node
{

    public function __construct(Twig_Node $position = null, Twig_Node $options = null, $lineno = 0, $tag = null)
    {
        parent::__construct([], array('position' => $position, 'options' => $options), $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$this->env->getExtension(\'%s\')->getManager()->render(\'%s\', ', AssetsInjectionExtension::NAME, $this->getAssetType()))
            ->subcompile($this->getAttribute('position'))
            ->write(', ')
            ->subcompile($this->getAttribute('options'))
            ->write(');')
            ->write("\n");
    }

    protected abstract function getAssetType();

}