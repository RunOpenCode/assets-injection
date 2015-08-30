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

use RunOpenCode\AssetsInjection\Contract\ResourceInterface;

abstract class AbstractResource implements ResourceInterface
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $sourceRoot;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var integer
     */
    protected $lastModified;

    /**
     * Default constructor.
     *
     * @param string $source Source for this resource.
     * @param array $options Options related to this resource.
     * @param string|null $sourceRoot Resource root path.
     * @param integer|null $lastModified Unix timestamp of last modification date of this resource.
     */
    public function __construct($source, array $options = [], $sourceRoot = null, $lastModified = null)
    {
        $this->source = $source;
        $this->sourceRoot = $sourceRoot;
        $this->options = $options;
        $this->lastModified = $lastModified;
    }

    public function getKey()
    {
        return md5($this->source);
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceRoot()
    {
        return $this->sourceRoot;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getSource();
    }
}