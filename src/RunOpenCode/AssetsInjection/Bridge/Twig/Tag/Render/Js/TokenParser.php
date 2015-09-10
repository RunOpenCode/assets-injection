<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Js;

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\AbstractTokenParser;
use Twig_Node;

/**
 * Class TokenParser
 *
 * Render injected javascripts
 *
 * {% js %}
 * {% js position %}
 * {% js using options %}
 * {% js position using options %}
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Js
 */
class TokenParser extends AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'js';
    }

    /**
     * {@inheritdoc}
     */
    protected function createNode(Twig_Node $position = null, Twig_Node $options = null, $lineno = 0, $tag = null)
    {
        return new Node($position, $options, $lineno, $tag);
    }
}