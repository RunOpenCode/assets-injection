<?php

namespace RunOpenCode\AssetsInjection\Contract;

interface ResourceRendererInterface
{
    public function render(array $resources, array $options = []);
}