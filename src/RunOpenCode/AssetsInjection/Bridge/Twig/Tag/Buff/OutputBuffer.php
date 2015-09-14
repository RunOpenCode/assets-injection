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

use ArrayObject;

/**
 * Class OutputBuffer
 *
 * Utility class - enables buffering of twig body and nodes asset injection nodes within. It contains collection of buffered
 * and non-buffered portions of templates and renders them in correct order.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff
 */
class OutputBuffer extends ArrayObject
{
    public function display(array $context, array $blocks)
    {
        for ($i = 0; $i < count($this); $i++) {
            if (is_callable($this[$i])) {
                echo call_user_func_array($this[$i], array($context, $blocks));
            } else {
                echo $this[$i];
            }
        }
    }
}