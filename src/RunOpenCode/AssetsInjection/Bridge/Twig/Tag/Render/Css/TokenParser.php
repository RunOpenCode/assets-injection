<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Css;

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\AbstractTokenParser;
use Twig_Node;

/**
 * Class TokenParser
 *
 * Render injected stylesheets
 *
 * {% css %}
 * {% css position %}
 * {% css using options %}
 * {% css position using options %}
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Css
 */
class TokenParser extends AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'css';
    }

    /**
     * {@inheritdoc}
     */
    protected function createNode(Twig_Node $position = null, Twig_Node $options = null, $lineno = 0, $tag = null)
    {
        return new Node($position, $options, $lineno, $tag);
    }
}