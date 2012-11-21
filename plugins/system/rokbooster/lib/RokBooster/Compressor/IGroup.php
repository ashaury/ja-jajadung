<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
interface RokBooster_Compressor_IGroup
{

	/**
	 *
	 */
	const STATE_IGNORE = 'ignore';
	/**
	 *
	 */
	const STATE_INCLUDE = 'include';

	/**
	 * @abstract
	 * @return mixed
	 */
	public function getChecksum();

	/**
	 * @abstract
	 * @return mixed
	 */
	public function cleanup();

	/**
	 * @abstract
	 * @return string
	 */
	public function getResult();

	/**
	 * @abstract
	 *
	 * @param $result
	 */
	public function setResult($result);


	/**
	 * @abstract
	 *
	 * @param $status
	 */
	public function setStatus($status);

	/**
	 * @abstract
	 * @return string
	 */
	public function getStatus();

	/**
	 * @abstract
	 *
	 * @param string[] $attributes
	 */
	public function setAttributes($attributes);

	/**
	 * @abstract
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function addAttribute($key, $value);

	/**
	 * @abstract
	 * @return string[]
	 */
	public function getAttributes();

	/**
	 * @abstract
	 *
	 * @param string $mime
	 */
	public function setMime($mime);

	/**
	 * @abstract
	 * @return string
	 */
	public function getMime();
}
