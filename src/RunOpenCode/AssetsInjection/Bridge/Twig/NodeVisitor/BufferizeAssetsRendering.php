<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\NodeVisitor;

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff\End;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff\Start;
use Twig_Node_Block;
use Twig_Node_BlockReference;
use Twig_Node_Body;
use Twig_Node_Module;
use Twig_Environment;
use Twig_Node;

final class BufferizeAssetsRendering extends BaseNodeVisitor
{
    private $currentScope;

    private $blocks;

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        parent::doEnterNode($node, $env);

        if ($this->shouldProcess()) {

            if ($node instanceof Twig_Node_Module) {
                $this->blocks = $node->getNode('blocks')->getIterator()->getArrayCopy();
            }

            if ($node instanceof Twig_Node_Body) {
                $this->currentScope = null;
            }

            if ($node instanceof Twig_Node_Block) {
                $this->currentScope = $node->getAttribute('name');
            }

        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Twig_Node $node, Twig_Environment $env)
    {
        if ($this->shouldProcess()) {

            if ($node instanceof Twig_Node_Module) {
                $this->blocks = null;
            }

            if (!$this->currentScope && $this->isAssetsInjectionNode($node)) {

                return new Twig_Node([
                    new Start(),
                    $node,
                    new End()
                ]);

            } elseif (!$this->currentScope && $node instanceof Twig_Node_BlockReference && $this->hasAssetsInjection($this->blocks[$node->getAttribute('name')])) {

                return new Twig_Node([
                    new Start(),
                    $node,
                    new End()
                ]);

            }

        }

        parent::doLeaveNode($node, $env);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 10;
    }

    private function hasAssetsInjection(Twig_Node $node)
    {
        if ($this->isAssetsInjectionNode($node)) {
            return true;
        }

        $has = false;

        foreach ($node as $k => $n) {

            if ($this->isAssetsInjectionNode($n)) {
                return true;
            } elseif($n instanceof Twig_Node_BlockReference && $this->hasAssetsInjection($this->blocks[$n->getAttribute('name')])) {
                return true;
            } else {
                $has = $has || $this->hasAssetsInjection($n);
            }
        }

        return $has;
    }
}