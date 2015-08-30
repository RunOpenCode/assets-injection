<?php

namespace RunOpenCode\AssetsInjection\Value;

abstract class PathType
{
    private final function __construct() { }

    const RAW = 'raw';
    const ABSOLUTE = 'absolute';
    const RELATIVE = 'relative';
}