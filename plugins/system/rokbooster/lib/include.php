<?php
/**
 * @version   $Id: include.php 314 2012-04-27 02:48:00Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!defined('ROKBOOSTER_LIB')) {
    /**
     * define rokbooster library dir
     */
    define('ROKBOOSTER_LIB_ROOT', dirname(__FILE__));
    /**
     * @param $className
     * @return bool
     */
    function rokbooster_classloader($className)
	{
		$toplevel      = strtok($className, "_");
		$compiled_path = ROKBOOSTER_LIB_ROOT . DIRECTORY_SEPARATOR . $toplevel . '.compiled.php';
		if (file_exists($compiled_path)) {
			require_once ($compiled_path);
			if (class_exists($className, false)) {
				return true;
			}
		}
		$filePath = ROKBOOSTER_LIB_ROOT . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		if (file_exists($filePath) && is_readable($filePath)) {
			require_once($filePath);
			return true;
		}
		return false;
	}

	spl_autoload_register('rokbooster_classloader');
    /**
     * define rokbooster library dir
     */
    define('ROKBOOSTER_LIB', '1.0.2');
}
