<?php

namespace RunOpenCode\AssetsInjection\Contract;

interface ManagerInterface
{
    public function setContainer(ContainerInterface $container);

    public function setResourceRenderer(ResourceRendererInterface $resourceRenderer);

    public function inject($name);

    public function render($type, $position = null, array $options = []);
}