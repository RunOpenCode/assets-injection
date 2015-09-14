<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Js;

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\AbstractRenderNode;

/**
 * Class Node
 *
 * Render injected js files.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Js
 */
final class Node extends AbstractRenderNode
{
    /**
     * {@inheritdoc}
     */
    protected function getAssetType()
    {
        return 'js';
    }
}