<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Factory;

use RunOpenCode\AssetsInjection\Container;
use RunOpenCode\AssetsInjection\Contract\Loader\LoaderInterface;
use RunOpenCode\AssetsInjection\Library\LibraryCollection;

class ContainerFactory
{
    public function build(array $loaders, array $directories, array $options = [])
    {
        $libraries = new LibraryCollection();

        /**
         * @var LoaderInterface $loader
         */
        foreach ($loaders as $loader) {

            foreach (($tmp = $loader->load($directories)) as $library) {
                $libraries[] = $library;
            }
        }

        return new Container($libraries, $options);
    }
}