<?php
/**
 * Constants:: provides the constants for this package.
 *
 * PHP version 5
 *
 * @category Horde
 * @package  Components
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Components;

/**
 * Constants:: provides the constants for this package.
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @package  Components
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Constants
{
    const DATA_DIR = '@data_dir@';
    const CFG_DIR  = '@cfg_dir@';

    /**
     * Return the position of the package data directory.
     *
     * @return string Path to the directory holding data files.
     */
    public static function getDataDirectory()
    {
        if (strpos(self::DATA_DIR, '@data_dir') === 0) {
            return __DIR__ . '/../../data';
        }
        return self::DATA_DIR . '/Components';
    }

    /**
     * Return the position of the package configuration file.
     *
     * @return string Path to the default configuration file.
     */
    public static function getConfigFile()
    {
        if (strpos(self::CFG_DIR, '@cfg_dir') === 0) {
            return __DIR__ . '/../../config/conf.php';
        }
        return self::CFG_DIR . '/conf.php';
    }
}
