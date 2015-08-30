<?php

namespace RunOpenCode\AssetsInjection\Template;

interface TemplateEngineInterface
{
    public function render($template, array $variables);
}