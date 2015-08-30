<?php

namespace RunOpenCode\AssetsInjection\Renderer;

use RunOpenCode\AssetsInjection\Contract\ResourceRendererInterface;

abstract class AbstractRenderer implements ResourceRendererInterface
{
    protected function getJavascriptIncludeHtml($src, array $attributes = [])
    {
        $attributes = array_merge(['type' => 'text/javascript'], $attributes);

        return sprintf('<script src="%s"%s></script>', $src, $this->parseHtmlAttributes($attributes));
    }

    protected function getJavascriptCodeHtml($code, array $attributes = [])
    {
        $attributes = array_merge(['type' => 'text/javascript'], $attributes);

        return sprintf('<script%s>%s</script>', $this->parseHtmlAttributes($attributes), $code);
    }

    protected function getStylesheetIncludeHtml($src, array $attributes = [])
    {
        $attributes = array_merge([
            'type' => 'text/css',
            'rel' => 'stylesheet',
        ], $attributes);

        return sprintf('<link href="%s"%s/>', $src, $this->parseHtmlAttributes($attributes));
    }

    protected function getStylesheetCodeHtml($code, array $attributes = [])
    {
        $attributes = array_merge([
            'type' => 'text/css'
        ], $attributes);

        return sprintf('<style%s>%s</style>', $code, $this->parseHtmlAttributes($attributes));
    }

    protected function parseHtmlAttributes(array $attributes = [])
    {
        $result = [];

        foreach ($attributes as $key => $value) {
            $result[] = sprintf('%s="%s"', $key, $value);
        }

        return (count($result) > 0) ? implode(' ', $result) : '';
    }
}