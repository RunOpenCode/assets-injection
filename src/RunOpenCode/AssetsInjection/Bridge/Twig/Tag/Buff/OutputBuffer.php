<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff;

class OutputBuffer extends \ArrayObject
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