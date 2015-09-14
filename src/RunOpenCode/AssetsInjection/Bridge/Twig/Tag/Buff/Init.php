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
use Twig_Compiler;

/**
 * Class Init
 *
 * Init buffering node - initialize body buffering.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff
 */
class Init extends Twig_Node
{
    /**
     * {@inheritdoc}
     */
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