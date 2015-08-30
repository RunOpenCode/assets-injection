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

class testDynamicContainerWithSequentialRenderer extends \RunOpenCode\AssetsInjection\Container implements ContainerInterface
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
            call_user_func(array($this, 'inject_' . str_replace(array('_', '-', '/'), '_', $name)));
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

   /**
    * Method which optimizes injection of resources of library 'jquery'.
    */
    private function inject_jquery()
    {
        $this->injected['libraries']['jquery'] = true;

        if (!array_key_exists('3303ee9439ee535d868ba965431ce188', $this->injected['resources'])) {
            $this->injected['resources']['3303ee9439ee535d868ba965431ce188'] = new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1440776997);
        }
    }

   /**
    * Method which optimizes injection of resources of library 'my-lib'.
    */
    private function inject_my_lib()
    {
        $this->injected['libraries']['my-lib'] = true;

        if (!array_key_exists('3303ee9439ee535d868ba965431ce188', $this->injected['resources'])) {
            $this->injected['resources']['3303ee9439ee535d868ba965431ce188'] = new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1440776997);
        }
        if (!array_key_exists('85b63b3eed182696ed96dff68304ea24', $this->injected['resources'])) {
            $this->injected['resources']['85b63b3eed182696ed96dff68304ea24'] = new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy/tipsy.css', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy', 1439466816);
        }
        if (!array_key_exists('90519327f464562bedcc19f4eb62c3c9', $this->injected['resources'])) {
            $this->injected['resources']['90519327f464562bedcc19f4eb62c3c9'] = new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy/tipsy.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy', 1439466791);
        }
        if (!array_key_exists('ecf95945aeecbb93948b29b7104a8af1', $this->injected['resources'])) {
            $this->injected['resources']['ecf95945aeecbb93948b29b7104a8af1'] = new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Public/js/RunOpenCode.DynamicAssetInclusion.min.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Public/js', 1439977254);
        }
        if (!array_key_exists('6a8f358b797913b751db0d604a1e84dd', $this->injected['resources'])) {
            $this->injected['resources']['6a8f358b797913b751db0d604a1e84dd'] = new \RunOpenCode\AssetsInjection\Resource\JavascriptStringResource(';(function(r){ r.DynamicAssetInclusion.loadJavascript(\'http://code.jquery.com/jquery-1.11.3.js\'); })(RunOpenCode);', [], null, null);
        }
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
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1440776997),
            ]),
            new FrozenLibraryDefinition('my-lib', [
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js/jquery.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/js', 1440776997),
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy/tipsy.css', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy', 1439466816),
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy/tipsy.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Tests/Data/web/lib/tipsy', 1439466791),
                new \RunOpenCode\AssetsInjection\Resource\FileResource('/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Public/js/RunOpenCode.DynamicAssetInclusion.min.js', [], '/Users/TheCelavi/Sites/RunOpenCode/Library Development/packages/assets-injection/src/RunOpenCode/AssetsInjection/Public/js', 1439977254),
                new \RunOpenCode\AssetsInjection\Resource\JavascriptStringResource(';(function(r){ r.DynamicAssetInclusion.loadJavascript(\'http://code.jquery.com/jquery-1.11.3.js\'); })(RunOpenCode);', [], null, null),
            ]),
        ]);
    }
    /**
     * Lazy loads ejection map of resources for faster ejection.
     *
     * @return array Resources typemap
     */
    private function generateEjectionTypeMap()
    {
        return array (
          'position' => 
          array (
            'js' => 
            array (
              '' => 
              array (
                '3303ee9439ee535d868ba965431ce188' => '3303ee9439ee535d868ba965431ce188',
                '90519327f464562bedcc19f4eb62c3c9' => '90519327f464562bedcc19f4eb62c3c9',
                'ecf95945aeecbb93948b29b7104a8af1' => 'ecf95945aeecbb93948b29b7104a8af1',
                '6a8f358b797913b751db0d604a1e84dd' => '6a8f358b797913b751db0d604a1e84dd',
              ),
            ),
            'css' => 
            array (
              '' => 
              array (
                '85b63b3eed182696ed96dff68304ea24' => '85b63b3eed182696ed96dff68304ea24',
              ),
            ),
          ),
          'type' => 
          array (
            'js' => 
            array (
              '3303ee9439ee535d868ba965431ce188' => '3303ee9439ee535d868ba965431ce188',
              '90519327f464562bedcc19f4eb62c3c9' => '90519327f464562bedcc19f4eb62c3c9',
              'ecf95945aeecbb93948b29b7104a8af1' => 'ecf95945aeecbb93948b29b7104a8af1',
              '6a8f358b797913b751db0d604a1e84dd' => '6a8f358b797913b751db0d604a1e84dd',
            ),
            'css' => 
            array (
              '85b63b3eed182696ed96dff68304ea24' => '85b63b3eed182696ed96dff68304ea24',
            ),
          ),
        );
    }

}