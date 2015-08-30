<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Inject;

use RunOpenCode\AssetsInjection\Bridge\Twig\AssetsInjectionExtension;
use Twig_Compiler;
use Twig_Node;

class Node extends Twig_Node
{
    public function __construct(array $libraries = [], array $vars = [], $lineno = 0, $tag = null)
    {
        parent::__construct([], array('libraries' => $libraries, 'vars' => $vars), $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $this->attributes['vars'] = array_map(function($elem) {
            return '$context["' . $elem . '"]';
        }, $this->attributes['vars']);

        $this->attributes['libraries'] = array_map(function($elem) {
            return '\''. $elem . '\'';
        }, $this->attributes['libraries']);

        $includes = array_merge(array_values($this->attributes['libraries']), array_values($this->attributes['vars']));

        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$this->env->getExtension(\'%s\')->getManager()', AssetsInjectionExtension::NAME))
            ->write("\n");

        foreach ($includes as $include) {

            $compiler
                ->addDebugInfo($this)
                ->raw('    ')
                ->write(sprintf('->inject(%s)',$include))
                ->write("\n")
            ;

        }

        $compiler->write(';')->write("\n");
    }
}