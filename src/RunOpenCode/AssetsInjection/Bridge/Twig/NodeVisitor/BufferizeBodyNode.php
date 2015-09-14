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

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff\Init;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Buff\Output;
use Twig_Node_Module;
use Twig_Environment;
use Twig_Node;

/**
 * Class BufferizeBodyNode
 *
 * Wraps body of the twig template with output buffer,
 * making possible to delay execution of portions of template logic.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\NodeVisitor
 */
final class BufferizeBodyNode extends BaseNodeVisitor
{
    /**
     * @var bool Denotes if current template body should be bufferized.
     */
    private $shouldBufferizeBody = false;

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        $node = parent::doEnterNode($node, $env);

        if ($this->shouldProcess()) {

            if ($this->isAssetsInjectionNode($node)) {
                $this->shouldBufferizeBody = true;
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

                if ($this->shouldBufferizeBody) {
                    $node->setNode('body', new Twig_Node(array(
                        new Init(),
                        $node->getNode('body'),
                        new Output()
                    )));
                }

                $this->shouldBufferizeBody = false;
            }

        }

        $node = parent::doLeaveNode($node, $env);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 9;
    }
}