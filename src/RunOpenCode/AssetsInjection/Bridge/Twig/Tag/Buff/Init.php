<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff;

use Twig_Node;
use Twig_Compiler;

class Init extends Twig_Node
{
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->write('$context[strtolower(get_class($this)) . \'assets_injection_buffer\'] = new \\RunOpenCode\\AssetsInjection\\Bridge\\Twig\\Tag\\Buff\\OutputBuffer();')
            ->write("\n");

        $compiler
            ->write('ob_start();')
            ->write("\n");

    }
}