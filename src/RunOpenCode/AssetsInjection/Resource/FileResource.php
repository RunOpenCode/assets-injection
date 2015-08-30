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

class FileResource extends AbstractResource
{
    public function __construct($source, array $options = [], $sourceRoot = null, $lastModified = null)
    {
        if (is_null($sourceRoot)) {
            $sourceRoot = dirname($source);
        }

        parent::__construct($source, $options, $sourceRoot, $lastModified);
    }

    public function getLastModified()
    {
        if ($this->lastModified === false) {
            return null;
        }

        if (is_null($this->lastModified)) {
            $this->lastModified = filemtime($this->source);
        }

        return $this->lastModified;
    }
}