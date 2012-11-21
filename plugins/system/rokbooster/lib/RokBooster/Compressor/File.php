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
class RokBooster_Compressor_File
{
	/**
	 * @var string
	 */
	public $file;
	/**
	 * @var string
	 */
	public $path;
	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $mime;

	/**
	 * @var string[] name/value attributes of a file for a script or style tag
	 */
	public $attributes = array();

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var int
	 */
	public $mtime = 0;

	/**
	 * @var int
	 */
	public $ctime = 0;

	/**
	 * @var int
	 */
	public $size = 0;

	/**
	 * @var bool
	 */
	public $external = false;

	/**
	 * @var bool
	 */
	public $ignored = false;

	/**
	 * @var string
	 */
	public $content;


	/**
	 * @param $file
	 * @param $root_url
	 * @param $root_path
	 */
	public function __construct($file, $root_url, $root_path)
	{
		$this->file     = $file;
		$this->external = self::isLinkExternal($this->file, $root_url);
		$this->path     = (!$this->external) ? self::getFileLink($file, $root_url, $root_path) : $file;
		$this->url      = (!$this->external) ? self::getFileLink($file, $root_url, $root_path, false) : $file;
		$this->type     = self::getExt($file);

		if (!$this->external && $this->isFullUrl($this->file))
		{
			$this->file = self::getRelativePath($this->file);
		}
		if ($this->external) {
			$this->ignored = true;
		} elseif (file_exists($this->path)) {
			list(, , , , , , , $this->size, , $this->mtime, $this->ctime, ,) = stat($this->path);
		}
	}


	/**
	 * @return bool
	 */
	public function hasChanged()
	{
		if ($this->external) return true;
		if (!file_exists($this->path)) return true;
		list(, , , , , , , $size, , $mtime, $ctime, ,) = stat($this->path);
		if ($this->mtime != $mtime || $this->ctime != $ctime || $this->size != $size) return true;
		return false;
	}


	protected static function isFullUrl($url)
	{
		$uri  = parse_url($url);
		if (isset($uri['scheme']) && (strtolower($uri['scheme']) == 'http' || strtolower($uri['scheme'] == 'https')))
		{
			return true;
		}
		return false;
	}

	protected static function getRelativePath($url)
	{
		$uri  = parse_url($url);
		$path = (isset($uri['path'])) ? $uri['path'] : '';
		return $path;
	}
	/**
	 * @param string $link   original relative url
	 * @param bool   $isPath specify path or url, path is default
	 *
	 * @param        $root_url
	 * @param        $root_path
	 *
	 * @return string $filepath return requested link as a full url or full path
	 */
	protected static function getFileLink($link, $root_url, $root_path, $isPath = true)
	{
		$uri  = parse_url($root_url);
		$path = (isset($uri['path'])) ? $uri['path'] : '';
		$base = str_replace($path, '', $root_url);
		if ($link && $base && strpos($link, $base) !== false) $link = str_replace($base, "", $link);
		if ($link && $path && strpos($link, $path) !== false) $link = str_replace($path, "", $link);
		if (substr($link, 0, 1) != DS) $link = DS . $link;
		$filepath = ($isPath) ? $root_path . $link : $root_url . $link;
		return $filepath;
	}

	protected static function isLinkExternal($url, $root_url)
	{
		$url_uri = parse_url($url);

		$ext = strtolower(pathinfo(basename($url_uri['path']), PATHINFO_EXTENSION));
		if ($ext != 'js' && $ext != 'css'){
			return true;
		}

		//if the url does not have a scheme must be internal=
		if (isset($url_uri['scheme']) && (strtolower($url_uri['scheme']) == 'http' || strtolower($url_uri['scheme'] == 'https'))) {
			$site_uri = parse_url($root_url);
			if (isset($url_uri['host']) && strtolower($url_uri['host']) == strtolower($site_uri['host'])) return false;
		}
		// cover external urls like //foo.com/foo.js
		if (!isset($url_uri['host']) && !isset($url_uri['scheme']) && isset($url_uri['path']) && substr($url_uri['path'], 0, 2) != '//') return false;
		//the url has a host and it isn't internal
		return true;
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	protected static function getExt($url)
	{
		$uri = pathinfo($url);
		return (isset($uri['extension'])) ? $uri['extension'] : '';
	}

	/**
	 * @param $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return mixed
	 */
	public function getContent()
	{
		if (!isset($this->content) && !$this->external) {
			if (file_exists($this->path) && is_readable($this->path)) {
				$this->content = file_get_contents($this->path);
			}
		} else {
			$this->ignored = true;
		}
		return $this->content;
	}

	/**
	 * @param $external
	 */
	public function setExternal($external = true)
	{
		$this->external = $external;
	}

	/**
	 * @return bool
	 */
	public function isExternal()
	{
		return $this->external;
	}

	/**
	 * @param $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}

	/**
	 * @return mixed
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param $ignored
	 */
	public function setIgnored($ignored = true)
	{
		$this->ignored = $ignored;
	}

	/**
	 * @return bool
	 */
	public function isIgnored()
	{
		return $this->ignored;
	}

	/**
	 * @param $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return mixed
	 */
	public function getCtime()
	{
		return $this->ctime;
	}

	/**
	 * @return mixed
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return mixed
	 */
	public function getMtime()
	{
		return $this->mtime;
	}

	/**
	 * @return mixed
	 */
	function __toString()
	{
		return $this->file;
	}

	/**
	 * @param $attributes
	 */
	public function setAttributes($attributes)
	{
		if (is_array($attributes)) {
			$this->attributes = $attributes;
			ksort($this->attributes);
		}
	}

	/**
	 * @return array|string[]
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function addAttribute($key, $value)
	{
		$this->attributes[$key] = $value;
		ksort($this->attributes);
		reset($this->attributes);
	}

	/**
	 * @param string $mime
	 */
	public function setMime($mime)
	{
		$this->mime = $mime;
	}

	/**
	 * @return string
	 */
	public function getMime()
	{
		return $this->mime;
	}
}
