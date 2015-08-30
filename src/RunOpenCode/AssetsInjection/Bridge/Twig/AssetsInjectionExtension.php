<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig;

use RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Inject\TokenParser as InjectTokenParser;
use RunOpenCode\AssetsInjection\Contract\ManagerInterface;
use RunOpenCode\AssetsInjection\Exception\InvalidArgumentException;
use RunOpenCode\AssetsInjection\Utils\AssetType;
use Twig_Extension;
use Twig_SimpleFunction;

final class AssetsInjectionExtension extends Twig_Extension
{
    const NAME = 'assets_injection';

    private $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

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
            }, ['is_safe' => true]),
            new Twig_SimpleFunction('css', function($position = null, array $options = []) use ($manager) {
                return $manager->render(AssetType::STYLESHEET, $position, $options);
            }, ['is_safe' => true])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new InjectTokenParser()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [
            new NodeVisitor()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}