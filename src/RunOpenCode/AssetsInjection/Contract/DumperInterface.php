<?php
namespace RunOpenCode\AssetsInjection\Contract;

interface DumperInterface
{
    public function dump(ContainerInterface $container, array $options = []);
}