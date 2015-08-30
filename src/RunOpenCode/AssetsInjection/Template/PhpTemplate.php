<?php
namespace RunOpenCode\AssetsInjection\Template;

use RunOpenCode\AssetsInjection\Contract\ResourceInterface;
use RunOpenCode\AssetsInjection\Exception\TemplateEngineException;
use RunOpenCode\AssetsInjection\Resource\FileResource;

class PhpTemplate implements TemplateEngineInterface
{
    public function render($template, array $variables)
    {
        extract($variables);

        ob_start();
        ob_implicit_flush(0);

        try {
            require($template);
        } catch (\Exception $e) {
            ob_end_clean();
            throw new TemplateEngineException(sprintf('ERROR: Could not render template "%s".', $template), 0, $e);
        }

        return ob_get_clean();
    }

    private function exportVariable($variable)
    {
        if (is_null($variable)) {
            return 'null';
        } elseif (is_array($variable) && count($variable) == 0) {
            return '[]';
        } else {
            return var_export($variable, true);
        }
    }
}