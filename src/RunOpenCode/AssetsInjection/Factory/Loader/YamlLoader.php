<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Factory\Loader;

use RunOpenCode\AssetsInjection\Contract\Loader\LoaderInterface;
use RunOpenCode\AssetsInjection\Exception\ConfigurationException;
use RunOpenCode\AssetsInjection\Library\LibraryDefinition;
use RunOpenCode\AssetsInjection\Resource\FileResource;
use RunOpenCode\AssetsInjection\Resource\GlobResource;
use RunOpenCode\AssetsInjection\Resource\HttpResource;
use RunOpenCode\AssetsInjection\Resource\ReferenceResource;
use Symfony\Component\Yaml\Parser;

final class YamlLoader implements LoaderInterface
{
    const DEFAULT_CONFIG_FILE = 'assets.yml';

    private $parser;

    private $filePatterns;

    public function __construct(array $filePatterns = [])
    {
        $this->parser = new Parser();
        if (count($filePatterns) > 0) {
            $this->filePatterns = $filePatterns;
        } else {
            $this->filePatterns = [self::DEFAULT_CONFIG_FILE];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $directories)
    {
        $result = [];

        foreach ($directories as $directory) {

            foreach ($this->filePatterns as $filePattern) {

                $configurations = glob(sprintf('%s%s%s', rtrim($directory, '/\\'), DIRECTORY_SEPARATOR, $filePattern));

                if (count($configurations)) {

                    foreach ($configurations as $configuration) {

                        /**
                         * @var LibraryDefinition $definition
                         */
                        foreach (($definitions = $this->buildDefinitions($configuration)) as $definition) {
                            $result[$definition->getName()] = $definition;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Builds definition from yml configuration.
     *
     * @param string $path Path to configuration file.
     * @return array<LibraryDefinition> Collection of built definitions.
     * @throws ConfigurationException
     */
    private function buildDefinitions($path)
    {
        $configuration = $this->parser->parse(file_get_contents($path));

        $result = [];

        foreach ($configuration as $libraryName => $libraryResources) {

            $library = new LibraryDefinition($libraryName);

            foreach ($libraryResources as $libraryResource) {

                if (!isset($libraryResource['source'])) {
                    throw new ConfigurationException(sprintf('Source for resource in configuration "%s" must be provided.', $path));
                }

                if (!is_string($libraryResource['source'])) {
                    throw new ConfigurationException(sprintf('Source for resource in configuration "%s" must be string, "%s" provided.', $path, $libraryResource['source']));
                }

                $source = $libraryResource['source'];
                $options = isset($libraryResource['options']) ? $libraryResource['options'] : [];

                if (!is_array($options)) {
                    throw new ConfigurationException(sprintf('Expected array of options for source "%s", "%s" given in configuration "%s".', $libraryResource['source'], gettype($libraryResource['options']), $path));
                }

                if ($source{0} === '@') {
                    $library->addResource(new ReferenceResource($source, $options));
                } elseif (strpos($source, '://') !== false || strpos($source, '//') === 0) {
                    $library->addResource(new HttpResource($source, $options));
                } else {
                    if (file_exists($source)) {
                        $library->addResource(new FileResource($source, $options));
                    } elseif (file_exists(($tmp = sprintf('%s%s%s', rtrim(dirname($path), '/\\'), DIRECTORY_SEPARATOR, $source)))) {
                        $library->addResource(new FileResource(realpath($tmp), $options));
                    } elseif (count(glob($source)) > 0) {
                        $library->addResource(new GlobResource($source, $options));
                    } elseif (count(glob(($tmp = sprintf('%s%s%s', rtrim(dirname($path), '/\\'), DIRECTORY_SEPARATOR, $source)))) > 0) {
                        $library->addResource(new GlobResource($tmp, $options));
                    } else {
                        throw new ConfigurationException(sprintf('Unable to build resource from given source "%s" given in configuration "%s".', $source, $path));
                    }
                }
            }

            $result[$libraryName] = $library;
        }

        return $result;
    }
}