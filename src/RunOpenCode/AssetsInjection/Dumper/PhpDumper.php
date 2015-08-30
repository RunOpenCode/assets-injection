<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Dumper;

use RunOpenCode\AssetsInjection\Contract\ContainerInterface;
use RunOpenCode\AssetsInjection\Contract\DumperInterface;
use RunOpenCode\AssetsInjection\Exception\RuntimeException;
use RunOpenCode\AssetsInjection\Template\PhpTemplate;

final class PhpDumper implements DumperInterface
{
    private $renderer;

    public function __construct()
    {
        $this->renderer = new PhpTemplate();
    }

    public function dump(ContainerInterface $container, array $options = [])
    {
        $variables = array_merge([
            'namespace' => null,
            'classname' => null,
            'extends' => '\\RunOpenCode\\AssetsInjection\\Container',
            'libraries' => $container->getLibraries(),
            'type' => DumperInterface::DYNAMIC_CONTAINER
        ], $options);

        if (!in_array($variables['type'], array(
            DumperInterface::DYNAMIC_CONTAINER,
            DumperInterface::STATIC_CONTAINER
        ))) {
            throw new RuntimeException(sprintf('Unknown container type "%s" requested for dumping.', $variables['template']));
        }

        return $this->renderer->render(implode(DIRECTORY_SEPARATOR, array(__DIR__, 'templates', sprintf('%s.template.php', $variables['type']))), $variables);
    }
}