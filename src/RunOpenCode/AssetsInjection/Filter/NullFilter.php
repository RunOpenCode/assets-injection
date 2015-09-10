<?php

namespace RunOpenCode\AssetsInjection\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class NullFilter implements FilterInterface
{

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        // Do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        // Do nothing
    }
}