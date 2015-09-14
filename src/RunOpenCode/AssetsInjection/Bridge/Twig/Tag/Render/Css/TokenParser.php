<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Css;

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\AbstractRenderTokenParser;
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
final class TokenParser extends AbstractRenderTokenParser
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