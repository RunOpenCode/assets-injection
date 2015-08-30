<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig;

use Twig_Environment;
use Twig_NodeInterface;

class NodeVisitor implements \Twig_NodeVisitorInterface
{

    /**
     * {@inheritdoc}
     */
    public function enterNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        // TODO: Implement enterNode() method.
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        // TODO: Implement leaveNode() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}