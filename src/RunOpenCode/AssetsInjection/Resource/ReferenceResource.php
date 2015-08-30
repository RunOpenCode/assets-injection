<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Resource;

class ReferenceResource extends AbstractResource
{
    public function __construct($source, array $options = [])
    {
        parent::__construct($source, $options);
    }
}