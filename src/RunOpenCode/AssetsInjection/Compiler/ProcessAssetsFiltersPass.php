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

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\StringAsset;
use Assetic\AssetWriter;
use Assetic\FilterManager;
use RunOpenCode\AssetsInjection\Contract\Compiler\CompilerPassInterface;
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryDefinitionInterface;
use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\JavascriptStringResource;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\StringResource;
use RunOpenCode\AssetsInjection\Resource\StylesheetStringResource;
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
     * FilterManager contains all registered and available filters. Options that defines behavior of this compiler pass:
     *
     *  * development: boolean - should compiler apply optional filters.
     *  * output_dir: string - where to output filtered assets.
     *
     * Notes:
     *
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
            'development_environment_extension_suffix' => 'dev',
            'production_environment_extension_suffix' => 'prod'
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

        $this->options['output_dir'] = rtrim($this->options['output_dir'], '/\\');

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
            /**
             * @var ResourceInterface $resource
             */
            foreach ($currentResources = $definition->getResources() as $resource) {

                if (isset($resource->getOptions()['filters']) && count($resource->getOptions()['filters'])) {
                    $definition->replaceResource($resource, [$this->filterResource($resource, $this->getFilters($resource))]);
                }

            }
        }

        return new CompilerPassResult($container);
    }

    /**
     * Filter resource.
     *
     * Filter resource, applying filters and saving output into output location.
     *
     * @param ResourceInterface $resource Resource to filter.
     * @return ResourceInterface Resulting filtered resources.
     */
    private function filterResource(ResourceInterface $resource, array $filters)
    {
        if ($resource instanceof FileResource) {
            $asset = new FileAsset($resource->getSource(), $filters);
        } elseif ($resource instanceof HttpResource) {
            $asset = new HttpAsset($resource->getSource(), $filters);
        } elseif ($resource instanceof StringResource) {
            $asset = new StringAsset($resource->getSource(), $filters, $resource->getSourceRoot());
        } else {
            throw new InvalidArgumentException(sprintf('Instance of "%s" expected, "%s" given.', implode('", "', array(
                FileResource::class,
                HttpResource::class,
                StringResource::class
            )), get_class($resource)));
        }

        $asset->setTargetPath($this->calculateTargetFilename($resource));

        $path = sprintf('%s%s%s', $this->options['output_dir'], DIRECTORY_SEPARATOR, $asset->getTargetPath());

        if (!file_exists($path) || filectime($path) !== $resource->getLastModified()) {
            $this->assetWriter->writeAsset($asset);
            touch($path, ($resource->getLastModified()) ? $resource->getLastModified() : time());
        }

        return new FileResource($path);
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
        if ($resource instanceof StringResource) {
            $filename = (isset($resource->getOptions()['filename'])) ? $resource->getOptions()['filename'] : $resource->getKey();

            if ($resource instanceof JavascriptStringResource) {
                $extension = AssetType::JAVASCRIPT;
            } elseif ($resource instanceof StylesheetStringResource) {
                $extension = AssetType::STYLESHEET;
            } else {
                throw new InvalidArgumentException(sprintf('Unable to determine resource type, instance of "%s" expected, "%s" given.', implode('", "', array(
                    JavascriptStringResource::class,
                    StylesheetStringResource::class
                )), get_class($resource)));
            }
        } else {
            $filename = pathinfo($resource->getSource(), PATHINFO_FILENAME);
            $extension = AssetType::guessAssetType($resource->getSource());
        }

        $environment = ($this->options['development'] ? $this->options['development_environment_extension_suffix'] : $this->options['production_environment_extension_suffix']);

        return sprintf('%s%s%s%s',
            $resource->getKey() . '.',
            $filename . '.',
            ($environment) ? $environment . '.' : '',
            $extension
        );
    }
}