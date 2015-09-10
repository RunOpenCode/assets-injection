<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Tests\Mockup;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Class DummyFilter
 *
 * Dummy filter which will add a marker string to asset source in order to check validity of executed test.
 *
 * @package RunOpenCode\AssetsInjection\Tests\Mockup
 */
class DummyFilter implements FilterInterface
{

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $asset->setContent(sprintf('Provided content has been filtered successfully by "\\RunOpenCode\\AssetsInjection\\Tests\\Mockup\\DummyFilter": "%s".', $asset->getContent()));
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {

    }
}