<?php

namespace RunOpenCode\AssetsInjection\Contract;

interface ResourceInterface
{
    public function getKey();

    public function getSource();

    public function getSourceRoot();

    public function getOptions();

    public function getLastModified();
}