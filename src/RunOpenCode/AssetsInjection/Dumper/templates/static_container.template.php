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

class <?php echo ($classname) ? $classname : 'CompiledStaticContainer'; ?> extends <?php echo $extends; ?> implements ContainerInterface
{
    /**
     * @var array Ejected log.
     */
    protected $ejected;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct(new FrozenLibraryCollection(), $options);
        $this->libraries = null;
        $this->ejected = [];
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
        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws LogicException Container ejection can not be executed against all un-ejected resources in this context.
     */
    public function eject($type, $position = null)
    {
        if ($position === false) {
            throw new LogicException('Container ejection can not be executed against all un-ejected resources in this context.');
        }

        if (!in_array(($type = strtolower($type)), [
            AssetType::JAVASCRIPT,
            AssetType::STYLESHEET
        ])) {
            throw new InvalidArgumentException(sprintf('You can not eject resources of unknown "%s" asset type.', $type));
        }

        $method = sprintf('eject_%s_%s', $type, ($position) ? $position : '');

        if (isset($this->ejected[$method])) {
            return [];
        }

        $this->ejected[$method] = true;

        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        } else {
            return [];
        }
    }

<?php

$inject = (isset($inject)) ? array_unique($inject) : call_user_func(function($libraries) {
    $result = [];

    foreach ($libraries as $library) {
        $result[] = $library->getName();
    }

    return $result;

}, $libraries);

$injectedResources = [];
$definedPositions = [];

foreach ($inject as $name) {

    foreach ($resources = $libraries->getDefinition($name)->getResources() as $resource) {
        if (!isset($injectedResources[$resource->getKey()])) {
            $injectedResources[$resource->getKey()] = $resource;

            $position = (isset($resource->getOptions()['position'])) ? $resource->getOptions()['position'] : '';

            if (!isset($definedPositions[$position])) {
                $definedPositions[$position] = $position;
            }
        }
    }
}

foreach (['js', 'css'] as $type):
    foreach ($definedPositions as $position):
?>
    /**
     * Optimized function for ejecting resources from container for type "<?php echo $type; ?>" and position "<?php echo $position ?>".
     *
     * @return array Ejected resources
     */
    private function eject_<?php echo $type ?>_<?php echo $position ?>()
    {
        return [
<?php
foreach ($injectedResources as $injectedResource):

    $resourcePosition = (isset($injectedResource->getOptions()['position'])) ? $injectedResource->getOptions()['position'] : '';

    switch ($resourceClass = ltrim(get_class($injectedResource), '\\')) {
        case 'RunOpenCode\AssetsInjection\Resource\FileResource':         // Fall trough
        case 'RunOpenCode\AssetsInjection\Resource\HttpResource':
            $resourceType = \RunOpenCode\AssetsInjection\Utils\AssetType::guessAssetType($injectedResource->getSource());
            break;
        case 'RunOpenCode\AssetsInjection\Resource\JavascriptStringResource':
            $resourceType = \RunOpenCode\AssetsInjection\Utils\AssetType::JAVASCRIPT;
            break;
        case 'RunOpenCode\AssetsInjection\Resource\StylesheetStringResource':
            $resourceType = \RunOpenCode\AssetsInjection\Utils\AssetType::STYLESHEET;
            break;
        default:
            throw new \RunOpenCode\AssetsInjection\Exception\RuntimeException(sprintf('Unknown resource type given for generating container.', $resourceClass));
            break;
    }

    if ($type == $resourceType && $position == $resourcePosition):
?>
            new \<?php echo $resourceClass ?>(<?php echo $this->exportVariable($injectedResource->getSource()) ?>, <?php echo $this->exportVariable($injectedResource->getOptions()); ?>, <?php echo $this->exportVariable($injectedResource->getSourceRoot()); ?>, <?php echo $this->exportVariable($injectedResource->getLastModified()); ?>),
<?php
    endif;

endforeach;
?>
        ];
    }

<?php
    endforeach;
endforeach;
?>
    /**
     * {@inheritdoc}
     * @throws LogicException Clearing container injection is not allowed in this context.
     */
    public function clear()
    {
        throw new LogicException('Container injection can not be cleared in this context.');
    }

    /**
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
                            switch ($resourceClass = ltrim(get_class($resource), '\\')) {
                                case 'RunOpenCode\AssetsInjection\Resource\FileResource':                     // Fall trough
                                case 'RunOpenCode\AssetsInjection\Resource\HttpResource':                     // Fall trough
                                case 'RunOpenCode\AssetsInjection\Resource\JavascriptStringResource':         // Fall trough
                                case 'RunOpenCode\AssetsInjection\Resource\StylesheetStringResource':         // Fall trough
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
}