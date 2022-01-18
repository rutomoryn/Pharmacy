<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl;

/**
 * Gives access to internationalization data.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class Intl
{
    /**
     * The number of resource bundles to buffer. Loading the same resource
     * bundle for n locales takes up n spots in the buffer.
     */
    const BUFFER_SIZE = 10;

    /**
     * The directory name of the currency data.
     */
    const CURRENCY_DIR = 'currencies';

    /**
     * The directory name of the language data.
     */
    const LANGUAGE_DIR = 'languages';

    /**
     * The directory name of the script data.
     */
    const SCRIPT_DIR = 'scripts';

    /**
     * The directory name of the locale data.
     */
    const LOCALE_DIR = 'locales';

    /**
     * The directory name of the region data.
     */
    const REGION_DIR = 'regions';

    /**
     * The directory name of the zone data.
     */
    public const TIMEZONE_DIR = 'timezones';

    /**
     * @var string|bool|null
     */
    private static $icuVersion = false;

    /**
     * @var string
     */
    private static $icuDataVersion = false;

    /**
     * Returns whether the intl extension is installed.
     *
     * @return bool Returns true if the intl extension is installed, false otherwise
     */
    public static function isExtensionLoaded(): bool
    {
        return class_exists('\ResourceBundle');
    }

    /**
     * Returns the version of the installed ICU library.
     *
     * @return string|null The ICU version or NULL if it could not be determined
     */
    public static function getIcuVersion(): ?string
    {
        if (false === self::$icuVersion) {
            if (!self::isExtensionLoaded()) {
                self::$icuVersion = self::getIcuStubVersion();
            } elseif (\defined('INTL_ICU_VERSION')) {
                self::$icuVersion = INTL_ICU_VERSION;
            } else {
                try {
                    $reflector = new \ReflectionExtension('intl');
                    ob_start();
                    $reflector->info();
                    $output = strip_tags(ob_get_clean());
                    preg_match('/^ICU version (?:=>)?(.*)$/m', $output, $matches);

                    self::$icuVersion = trim($matches[1]);
                } catch (\ReflectionException $e) {
                    self::$icuVersion = null;
                }
            }
        }

        return self::$icuVersion;
    }

    /**
     * Returns the version of the installed ICU data.
     *
     * @return string The version of the installed ICU data
     */
    public static function getIcuDataVersion(): string
    {
        if (false === self::$icuDataVersion) {
            self::$icuDataVersion = trim(file_get_contents(self::getDataDirectory().'/version.txt'));
        }

        return self::$icuDataVersion;
    }

    /**
     * Returns the ICU version that the stub classes mimic.
     *
     * @return string The ICU version of the stub classes
     */
    public static function getIcuStubVersion(): string
    {
        return '65.1';
    }

    /**
     * Returns the absolute path to the data directory.
     *
     * @return string The absolute path to the data directory
     */
    public static function getDataDirectory(): string
    {
        return __DIR__.'/Resources/data';
    }

    /**
     * This class must not be instantiated.
     */
    private function __construct()
    {
    }
}
