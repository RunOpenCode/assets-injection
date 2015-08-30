<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Compiler;

use Assetic\Asset\FileAsset;
use Assetic\AssetWriter;
use Assetic\FilterManager;
use RunOpenCode\AssetsInjection\Contract\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use RunOpenCode\AssetsInjection\Value\CompilerPassResult;

/**
 * Class ProcessAssetsFiltersPass
 *
 * Process resources by applying filters and dumping content in output location.
 *
 * @package RunOpenCode\AssetsInjection\Compiler
 */
final class ProcessAssetsFiltersPass implements CompilerPassInterface
{
    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var AssetWriter
     */
    protected $assetWriter;

    /**
     * @var array
     */
    protected $options;

    /**
     * A constructor.
     *
     * FilterManager contains all registered and available filters. Options defines behavior of this compiler pass:
     *
     *  * development: boolean - should compiler apply optional filters.
     *  * output_dir: string - where to output filtered assets.
     *
     * Notes:
     *
     *  * Even if asset should not be filtered, its content is copied to output dir.
     *  * Resulting filename will have source filename in it (if possible), for sake of reference and debugging.
     *  * Resulting filename will have either "prod" or "dev" in name, for sake of reference and debugging.
     *  * Resulting filename will have resource key.
     *
     * @param FilterManager $filterManager Filter manager for assets.
     * @param array $options Settings.
     */
    public function __construct(FilterManager $filterManager, array $options)
    {
        $this->filterManager = $filterManager;
        $this->options = array_merge(array(
            'development' => false,
        ), $options);

        if (!isset($this->options['output_dir'])) {
            throw new \RuntimeException('You have to specify output dir.');
        }

        if (!is_dir($this->options['output_dir'])) {
            throw new \RuntimeException(sprintf('Provided output dir "%s" is not valid.', $this->options['output_dir']));
        }

        if (!is_writable($this->options['output_dir'])) {
            throw new \RuntimeException(sprintf('Provided output dir "%s" is not writable.', $this->options['output_dir']));
        }

        $this->assetWriter = new AssetWriter($this->options['output_dir']);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        /**
         * @var LibraryDefinitionInterface $definition
         */
        foreach ($definitions = $container->getLibraries()->getDefinitions() as $definition) {

            $resources = [];

            /**
             * @var ResourceInterface $resource
             */
            foreach ($currentResources = $definition->getResources() as $resource) {

                if (!$resource instanceof ReferenceResource) {
                    $definition->replaceResource($resource, $this->processResource($resource));
                }
            }
        }

        return new CompilerPassResult($container);
    }

    /**
     * Process resource.
     *
     * Process single resource, applying filters and saving output into output location.
     *
     * @param ResourceInterface $resource Resource to filter.
     * @return array<ResourceInterface> Resulting filtered resources.
     */
    private function processResource(ResourceInterface $resource)
    {
        if ($resource instanceof FileResource) {
            $asset = new FileAsset(
                $resource->getSource(),
                $this->getFilters($resource)
            );
        }

//
//
//
//        $filters = $this->getFilters($resource);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//        $options = $resource->getOptions();
//        $assets = [];
//
//        if (Asset::isLocal($resource)) {
//            $assets[] = new FileAsset($resource->getSource(), $filters);
//        } elseif (Asset::isRemote($resource)) {
//            $assets[] = new HttpAsset($resource->getSource(), $filters);
//        } elseif (Asset::isGlob($resource)) {
//
//            foreach ($files = glob($resource->getSource()) as $file) {
//                $assets[] = new FileAsset($file, $filters);
//            }
//
//        } else {
//            throw new \InvalidArgumentException(sprintf('Unsupported resource provided with source "%s".', $resource->getSource()));
//        }
//
//        $results = [];
//
//        // Remove applied filters in order to avoid double filtering.
//        if (count($filters)) {
//            $options['filters'] = array_diff($options['filters'], array_keys($filters));
//        }
//
//        /**
//         * @var AssetInterface $asset
//         */
//        foreach ($assets as $asset) {
//            $asset->setTargetPath($this->calculateTargetFilename($asset));
//            $this->assetWriter->writeAsset($asset);
//            $results[] = new Resource($asset->getTargetPath(), $options);
//        }
//
//        return $results;
    }

    /**
     * Get filters to apply against resource.
     *
     * @param ResourceInterface $resource Resource with filters.
     * @return array Collection of filters to apply on resource.
     */
    private function getFilters(ResourceInterface $resource)
    {
        if (isset($resource->getOptions()['filters']) && count(($resource->getOptions()['filters']))) {

            $result = [];

            foreach ($filters = $resource->getOptions()['filters'] as $filter) {

                if (strpos($filter, '?') !== 0 || (strpos($filter, '?') === 0 && !$this->options['development'])) {
                    $result[$filter] = $this->filterManager->get(ltrim($filter, '?'));
                }
            }

            return $result;

        } else {
            return [];
        }
    }

    /**
     * Calculates new file name for resource that will be dumped onto new location.
     *
     * @param ResourceInterface $resource Resource which will be dumped in new location.
     * @return string New file name.
     */
    private function calculateTargetFilename(ResourceInterface $resource)
    {

        $filename = pathinfo($asset->getSourcePath(), PATHINFO_FILENAME);
        $environment = ($this->options['development'] ? 'dev' : 'prod');
        $extension = AssetType::guessAssetType($asset->getSourcePath());

        return sprintf('%s.%s.%s.%s',
            md5(sprintf('%s%s%s%s', $asset->getContent(), $filename, $environment, $extension)),
            $filename,
            $environment,
            $extension
        );
    }
}