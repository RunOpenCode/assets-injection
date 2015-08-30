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

class HttpResource extends AbstractResource
{
    public function getLastModified()
    {
        if ($this->lastModified === false) {
            return null;
        }

        if (is_null($this->lastModified)) {

            if (false !== @file_get_contents($this->source, false, stream_context_create(array('http' => array('method' => 'HEAD'))))) {
                foreach ($http_response_header as $header) {
                    if (0 === stripos($header, 'Last-Modified: ')) {
                        list(, $mtime) = explode(':', $header, 2);

                        $this->lastModified = strtotime(trim($mtime));

                        return $this->lastModified;
                    }
                }
            }

            $this->lastModified = false;

            return null;
        }

        return $this->lastModified;
    }
}