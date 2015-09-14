<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Bridge\Twig\NodeVisitor;

use Twig_BaseNodeVisitor;
use Twig_Environment;
use Twig_Node_Expression_Function;
use Twig_Node;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Js\Node as JsNode;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Css\Node as CssNode;
use Twig_Node_Module;

/**
 * Class BaseNodeVisitor
 *
 * Base node visitor provides common methods for asset injection node visitors.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\NodeVisitor
 */
abstract class BaseNodeVisitor extends Twig_BaseNodeVisitor
{
    /**
     * @var array List of templates that should be processed.
     */
    protected $whitelist;

    /**
     * @var string Current template name.
     */
    protected $filename;

    /**
     * A constructor.
     *
     * @param array $whitelist List of templates that should be processed. Empty array means that all should be processed.
     */
    public function __construct(array $whitelist = [])
    {
        $this->whitelist = $whitelist;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        if ($node instanceof Twig_Node_Module) {
            $this->filename = $node->getAttribute('filename');
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
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