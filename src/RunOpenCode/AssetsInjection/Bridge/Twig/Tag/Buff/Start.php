<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff;

use Twig_Compiler;
use Twig_Node;

class Start extends Twig_Node
{
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->write('$context[strtolower(get_class($this)) . \'assets_injection_buffer\'][] = ob_get_clean();')
            ->write("\n");

        $compiler
            ->write('$context[strtolower(get_class($this)) . \'assets_injection_buffer\'][] = Closure::bind(function($context, $blocks) { ')
            ->write("\n");
    }
}