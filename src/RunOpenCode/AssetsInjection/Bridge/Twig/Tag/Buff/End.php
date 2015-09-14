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

use Twig_Node;

/**
 * Class End
 *
 * End buffering of subtree nodes.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff
 */
class End extends Twig_Node
{
    /**
     * {@inheritdoc}
     */
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