<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\LibraryCollectionInterface;
use RunOpenCode\AssetsInjection\Library\FrozenLibraryCollection;
use RunOpenCode\AssetsInjection\Library\FrozenLibraryDefinition;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use RunOpenCode\AssetsInjection\Exception\LogicException;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;

class CompiledStaticContainer extends \RunOpenCode\AssetsInjection\Container implements ContainerInterface
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

    /**
     * Optimized function for ejecting resources from container for type "js" and position "".
     *
     * @return array Ejected resources
     */
    private function eject_js_()
    {
        return [
            new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439466756),
            new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/myjavascript.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439572737),
        ];
    }

    /**
     * Optimized function for ejecting resources from container for type "css" and position "".
     *
     * @return array Ejected resources
     */
    private function eject_css_()
    {
        return [
        ];
    }

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
            new FrozenLibraryDefinition('jquery', [
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439466756),
            ]),
            new FrozenLibraryDefinition('my-lib', [
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439466756),
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/myjavascript.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439572737),
            ]),
            new FrozenLibraryDefinition('my-other-lib', [
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439466756),
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/myjavascript.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439572737),
                new \RunOpenCode\AssetsInjection\Resource\HttpResource('//code.jquery.com/jquery-1.11.3.js', [], null, null),
            ]),
            new FrozenLibraryDefinition('my-third-lib', [
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439466756),
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/myjavascript.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1439572737),
                new \RunOpenCode\AssetsInjection\Resource\HttpResource('//code.jquery.com/jquery-1.11.3.js', [], null, null),
                new \RunOpenCode\AssetsInjection\Resource\JavascriptStringResource(';(function(r){ r.DynamicAssetInclusion.loadJavascript(\'http://code.jquery.com/jquery-1.11.3.js\'); })(RunOpenCode);', [], null, null),
            ]),
        ]);
    }
}