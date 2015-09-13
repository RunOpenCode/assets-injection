<?php

namespace RunOpenCode\AssetsInjection\Contract\Loader;

use RunOpenCode\AssetsInjection\Exception\ConfigurationException;

interface LoaderInterface
{
    /**
     * Load asset configurations from given list of directories.
     *
     * @param array $directories Directories to scan for configuration files.
     * @return array<LibraryDefinition> List of loaded library definitions.
     * @throws ConfigurationException
     */
    public function load(array $directories);
}