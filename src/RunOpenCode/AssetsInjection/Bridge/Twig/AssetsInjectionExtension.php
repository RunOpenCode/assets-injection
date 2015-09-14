<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Bridge\Twig;

use RunOpenCode\AssetsInjection\Bridge\Twig\NodeVisitor\BufferizeAssetsRendering;
use RunOpenCode\AssetsInjection\Bridge\Twig\NodeVisitor\BufferizeBodyNode;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Inject\TokenParser as InjectTokenParser;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Css\TokenParser as CssTokenParser;
use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render\Js\TokenParser as JsTokenParser;
use RunOpenCode\AssetsInjection\Contract\ManagerInterface;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class AssetsInjectionExtension
 *
 * Assets injection Twig extension.
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig
 */
final class AssetsInjectionExtension extends Twig_Extension
{
    const NAME = 'assets_injection';

    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var array
     */
    private $options;

    /**
     * A constructor.
     *
     * Supported options:
     *
     * * bufferize - weather node visitors should be executed ensuring that assets injection tags are rendered last. Default
     * value is TRUE. For optimization purposes, array of template names can be provided as well, which will ensure that only
     * those templates will be processed.
     *
     * @param ManagerInterface $manager Manager.
     * @param array $options Array of options.
     */
    public function __construct(ManagerInterface $manager, array $options = [])
    {
        $this->manager = $manager;
        $this->options = array_merge(
            [
                'bufferize' => true
            ],
            $options
        );

        if ($this->options['bufferize'] === true) {
            $this->options['bufferize'] = [];
        }
    }

    /**
     * Get manager.
     *
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $manager = $this->manager;

        return [
            new Twig_SimpleFunction('inject', function() use ($manager) {
                $args = func_get_args();

                if (count($args) < 2) {
                    throw new InvalidArgumentException('You have to provide name of the asset library that you want to inject.');
                }

                for ($i = 1; $i < count($args); $i++) {
                    $manager->inject($args[$i]);
                }
            }),
            new Twig_SimpleFunction('js', function($position = null, array $options = []) use ($manager) {
                return $manager->render(AssetType::JAVASCRIPT, $position, $options);
            }),
            new Twig_SimpleFunction('css', function($position = null, array $options = []) use ($manager) {
                return $manager->render(AssetType::STYLESHEET, $position, $options);
            })
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new InjectTokenParser(),
            new CssTokenParser(),
            new JsTokenParser()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        if ($this->options['bufferize'] !== false && !is_null($this->options['bufferize'])) {

            return [
                new BufferizeBodyNode($this->options['bufferize']),
                new BufferizeAssetsRendering($this->options['bufferize'])
            ];

        } else {
            return [];
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}