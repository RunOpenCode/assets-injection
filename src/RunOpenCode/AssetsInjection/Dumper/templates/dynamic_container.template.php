<?php echo '<?php' . "\n"; ?>
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
<?php if ($namespace): ?>
namespace <?php echo $namespace . "\n";  ?>;
<?php endif; ?>
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryCollectionInterface;
use RunOpenCode\AssetsInjection\Library\FrozenLibraryCollection;
use RunOpenCode\AssetsInjection\Library\FrozenLibraryDefinition;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use RunOpenCode\AssetsInjection\Exception\LogicException;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;

class <?php echo ($classname) ? $classname : 'CompiledDynamicContainer'; ?> extends <?php echo $extends; ?> implements ContainerInterface
{
    /**
     * @var array Injected definitions.
     */
    protected $injected;

    /**
     * @var array Optimized map of resources for faster ejection.
     */
    protected $typemap;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct(new FrozenLibraryCollection(), $options);
        $this->libraries = null;
    }

    /**
     * {@inheritdoc}
     * @throws LogicException Library collection is immutable in compiled container.
     */
    public function setLibraries(LibraryCollectionInterface $manager)
    {
        throw new LogicException('Library collection can not be replaced in this context.');
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraries()
    {
        if (is_null($this->libraries)) {
            $this->lazyLoadDefinitionLibrary();
        }

        return $this->libraries;
    }

    /**
     * {@inheritdoc}
     */
    public function inject($name)
    {
        if (!isset($this->injected['libraries'][$name])) {
            call_user_func(array($this, 'inject_' . str_replace(array('-', '/'), '_', $name)));
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eject($type, $position = null)
    {
        if (!$this->typemap) {
            $this->typemap = $this->generateEjectionTypeMap();
        }

        if (!in_array(($type = strtolower($type)), [
            AssetType::JAVASCRIPT,
            AssetType::STYLESHEET
        ])) {
            throw new InvalidArgumentException(sprintf('You can not eject resources of unknown "%s" asset type.', $type));
        }

        $ejected = [];

        if ($position === false) {

            foreach ($this->injected['resources'] as $key => $value) {
                if (is_null($value)) {
                    continue;
                }

                if (isset($this->typemap['type'][$type][$key])) {
                    $ejected[] = $value;
                    $this->injected['resources'][$key] = null;
                }
            }

        } else {

            foreach ($this->injected['resources'] as $key => $value) {
                if (is_null($value)) {
                    continue;
                }

                if (isset($this->typemap['position'][$type][$position]) && isset($this->typemap['position'][$type][$position][$key])) {
                    $ejected[] = $value;
                    $this->injected['resources'][$key] = null;
                }
            }

        }

        return $ejected;
    }

<?php
$libraryMap = [];
foreach ($libraries as $library):

if (!isset($libraryMap[str_replace(array('-', '/'), '_', $library->getName())])) {
    $libraryMap[str_replace(array('-', '/'), '_', $library->getName())] = $library->getName();
} else {
    throw new \RunOpenCode\AssetsInjection\Exception\RuntimeException(sprintf('Library name collision detected for library "%s" and "%s".', $libraryMap[str_replace(array('-', '/'), '_', $library->getName())], $library->getName()));
}
?>   /**
    * Method which optimizes injection of resources of library '<?php echo $library->getName(); ?>'.
    */
    private function inject_<?php echo str_replace(array('-', '/'), '_', $library->getName()); ?>()
    {
        $this->injected['libraries']['<?php echo $library->getName(); ?>'] = true;

<?php foreach ($library->getResources() as $resource): ?>
        if (!array_key_exists(<?php echo $this->exportVariable($resource->getKey()) ?>, $this->injected['resources'])) {
            $this->injected['resources'][<?php echo $this->exportVariable($resource->getKey()) ?>] = new \<?php echo get_class($resource) ?>(<?php echo $this->exportVariable($resource->getSource()) ?>, <?php echo $this->exportVariable($resource->getOptions()); ?>, <?php echo $this->exportVariable($resource->getSourceRoot()); ?>, <?php echo $this->exportVariable($resource->getLastModified()); ?>);
        }
<?php endforeach; ?>
    }

<?php
endforeach;
?>    /**
     * Lazy loads definition library collection to container.
     *
     * @returns FrozenLibraryCollection Library connection.
     */
    private function lazyLoadDefinitionLibrary()
    {
        $this->libraries = new FrozenLibraryCollection([
<?php
            foreach ($libraries as $library):
?>
            new FrozenLibraryDefinition('<?php echo $library->getName(); ?>', [
<?php
                foreach ($library->getResources() as $resource):
                            switch ($resourceClass = get_class($resource)) {
                                case \RunOpenCode\AssetsInjection\Resource\FileResource::class:                     // Fall trough
                                case \RunOpenCode\AssetsInjection\Resource\HttpResource::class:                     // Fall trough
                                case \RunOpenCode\AssetsInjection\Resource\JavascriptStringResource::class:         // Fall trough
                                case \RunOpenCode\AssetsInjection\Resource\StylesheetStringResource::class:         // Fall trough
?>
                new \<?php echo $resourceClass ?>(<?php echo $this->exportVariable($resource->getSource()) ?>, <?php echo $this->exportVariable($resource->getOptions()); ?>, <?php echo $this->exportVariable($resource->getSourceRoot()); ?>, <?php echo $this->exportVariable($resource->getLastModified()); ?>),
<?php
                                    break;
                                default:
                                    throw new \RunOpenCode\AssetsInjection\Exception\RuntimeException(sprintf('Unknown resource type given for generating container.', $resourceClass));
                                    break;
                            }

                endforeach;
?>
            ]),
<?php endforeach; ?>
        ]);
    }
    /**
     * Lazy loads ejection map of resources for faster ejection.
     *
     * @return array Resources typemap
     */
    private function generateEjectionTypeMap()
    {
<?php

$map = ['position' => ['js' => [], 'css' => []], 'type' => ['js' => [], 'css' => []]];

foreach ($libraries as $library):
    /**
     * @var \RunOpenCode\AssetsInjection\Contract\ResourceInterface $resource
     */
    foreach ($library->getResources() as $resource):

        $position = (isset($resource->getOptions()['position'])) ? $resource->getOptions()['position'] : '';

        switch ($resourceClass = get_class($resource)) {
            case \RunOpenCode\AssetsInjection\Resource\FileResource::class:         // Fall trough
            case \RunOpenCode\AssetsInjection\Resource\HttpResource::class:
                $type = \RunOpenCode\AssetsInjection\Utils\AssetType::guessAssetType($resource->getSource());
                break;
            case \RunOpenCode\AssetsInjection\Resource\JavascriptStringResource::class:
                $type = \RunOpenCode\AssetsInjection\Utils\AssetType::JAVASCRIPT;
                break;
            case \RunOpenCode\AssetsInjection\Resource\StylesheetStringResource::class:
                $type = \RunOpenCode\AssetsInjection\Utils\AssetType::STYLESHEET;
                break;
            default:
                throw new \RunOpenCode\AssetsInjection\Exception\RuntimeException(sprintf('Unknown resource type given for generating container.', $resourceClass));
                break;
        }

        if (!array_key_exists($position, $map['position'][$type])) {
            $map['position'][$type][$position] = [];
        }

        if (!array_key_exists($resource->getKey(), $map['position'][$type][$position])) {
            $map['position'][$type][$position][$resource->getKey()] = $resource->getKey();
        }

        if (!array_key_exists($resource->getKey(), $map['type'][$type])) {
            $map['type'][$type][$resource->getKey()] = $resource->getKey();
        }

    endforeach;
endforeach;

?>
        return <?php echo ltrim(preg_replace('/^/m', '        ', $this->exportVariable($map))); ?>;
    }

}