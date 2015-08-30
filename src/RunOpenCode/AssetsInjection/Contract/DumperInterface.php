<?php
namespace RunOpenCode\AssetsInjection\Contract;

interface DumperInterface
{
    const DYNAMIC_CONTAINER = 'dynamic_container';
    const STATIC_CONTAINER = 'static_container';

    public function dump(ContainerInterface $container, array $options = []);
}