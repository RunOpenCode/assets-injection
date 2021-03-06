<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff;

use Twig_Compiler;
use Twig_Node;

/**
 * Class Start
 *
 * Start with buffering of subtree nodes.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff
 */
class Start extends Twig_Node
{
    /**
     * {@inheritdoc}
     */
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