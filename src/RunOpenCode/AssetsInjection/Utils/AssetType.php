<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Utils;

abstract class AssetType
{
    const STYLESHEET = 'css';
    const JAVASCRIPT = 'js';

    /**
     * Functional class.
     */
    private final function __construct() { }

    /**
     * @var array
     */
    private static $typeMap = [
        'less' => self::STYLESHEET,
        'sass' => self::STYLESHEET,
        'scss' => self::STYLESHEET,
        'css' => self::STYLESHEET,
        'styl' => self::STYLESHEET,
        'js' => self::JAVASCRIPT,
        'coffee' => self::JAVASCRIPT,
        'ts' => self::JAVASCRIPT
    ];

    /**
     * Get extension (without dot) from file name.
     *
     * @param string $file File from which extension should be extracted.
     * @return string|null Extension or NULL if it can not be determined.
     */
    public static function guessExtension($file)
    {
        if ($ext = @pathinfo($file, PATHINFO_EXTENSION)) {
            return strtolower($ext);
        } else {
            $path = explode('.', $file);
            if (count($path) > 1) {
                $path = end($path);

                if (
                    strpos($path, '/') !== false
                    ||
                    strpos($path, '\\') !== false
                ) {
                    return null;
                } else {
                    return strtolower($path);
                }
            } else {
                return null;
            }
        }
    }

    /**
     * Get type of the asset based on filename.
     *
     * @param string $file File from which type should be extracted.
     * @return string|null AssetType::STYLESHEET or AssetType::JAVASCRIPT, or NULL if it can not be determined.
     */
    public static function guessAssetType($file)
    {
        if (isset(self::$typeMap[self::guessExtension($file)])) {
            return self::$typeMap[self::guessExtension($file)];
        } else {
            return null;
        }
    }

    /**
     * Register new extension type for assets.
     *
     * @param string $extension New extension.
     * @param string $type AssetType::STYLESHEET or AssetType::JAVASCRIPT
     */
    public static function registerAssetType($extension, $type)
    {
        $extension = ltrim(strtolower($extension), '.');
        $type = strtolower($type);

        if (!in_array($type, array(
            self::STYLESHEET,
            self::JAVASCRIPT
        ))) {
            throw new \RuntimeException(sprintf('Provided type "%s" is unknown and can not be registered.', $type));
        }

        if (isset(self::$typeMap[$extension]) && self::$typeMap[$extension] != $type) {
            throw new \RuntimeException(sprintf('You can not overwrite already registered type ("%s" to "%s").', $extension, $type));
        }

        self::$typeMap[$extension] = $type;
    }
}