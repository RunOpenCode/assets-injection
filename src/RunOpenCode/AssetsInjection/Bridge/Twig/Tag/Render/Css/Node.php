<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Css;

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\AbstractNode;

final class Node extends AbstractNode
{
    /**
     * {@inheritdoc}
     */
    protected function getAssetType()
    {
        return 'css';
    }
}