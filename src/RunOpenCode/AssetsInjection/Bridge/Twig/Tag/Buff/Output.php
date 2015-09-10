<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff;

use Twig_Node;
use Twig_Compiler;

class Output extends Twig_Node
{
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->write('$context[strtolower(get_class($this)) . \'assets_injection_buffer\'][] = ob_get_clean();')
            ->write("\n");

        $compiler
            ->write('$context[strtolower(get_class($this)) . \'assets_injection_buffer\']->display($context, $blocks);')
            ->write("\n");
    }
}