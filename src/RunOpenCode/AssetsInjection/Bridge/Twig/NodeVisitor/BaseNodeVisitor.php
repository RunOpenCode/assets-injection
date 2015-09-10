<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\NodeVisitor;

use Twig_BaseNodeVisitor;
use Twig_Environment;
use Twig_Node_Expression_Function;
use Twig_Node;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Js\Node as JsNode;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Css\Node as CssNode;
use Twig_Node_Module;

abstract class BaseNodeVisitor extends Twig_BaseNodeVisitor
{
    protected $whitelist;

    protected $filename;

    public function __construct(array $whitelist = [])
    {
        $this->whitelist = $whitelist;
    }

    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        if ($node instanceof Twig_Node_Module) {
            $this->filename = $node->getAttribute('filename');
        }

        return $node;
    }

    protected function doLeaveNode(Twig_Node $node, Twig_Environment $env)
    {
        if ($node instanceof Twig_Node_Module) {
            $this->filename = null;
        }

        return $node;
    }

    /**
     * Check if current template should be processed with node visitor.
     *
     * @return bool
     */
    protected function shouldProcess()
    {
        if (count($this->whitelist) == 0) {
            return true;
        } else {
            return in_array($this->filename, $this->whitelist);
        }
    }

    /**
     * Check if provided node is AssetsInjection node.
     *
     * @param \Twig_Node $node
     * @return bool
     */
    protected function isAssetsInjectionNode(Twig_Node $node)
    {
        if ($node instanceof JsNode) {
            return true;
        } elseif ($node instanceof CssNode) {
            return true;
        } elseif ($node instanceof Twig_Node_Expression_Function) {
            if (
                $node->hasAttribute('name')
                &&
                (
                    $node->getAttribute('name') == 'css'
                    ||
                    $node->getAttribute('name') == 'js'
                )
            ) {
                return true;
            }
        }

        return false;
    }
}