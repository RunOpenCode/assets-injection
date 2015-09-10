<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff;

use Twig_Node;

class End extends Twig_Node
{
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->write('}, $this);')
            ->write("\n");

        $compiler
            ->write('ob_start();')
            ->write("\n");
    }
}