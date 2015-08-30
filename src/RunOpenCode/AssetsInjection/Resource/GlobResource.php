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

class GlobResource extends AbstractResource
{
    protected $files = null;

    public function getFiles()
    {
        if (is_null($this->files)) {
            $this->files = @glob($this->source);
        }

        return $this->files;
    }

}