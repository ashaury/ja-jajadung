<?php
/**
 * @version   $Id: rokbooster.php 2071 2012-07-31 22:33:00Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once ('lib/include.php');

/**
 * RokBooster plugin
 * @package        RokBooster
 * @subpackage     System
 */
class plgSystemRokBooster extends JPlugin
{
	const CACHE_GROUP             = 'rokbooster';
	const GENERATOR_STATE_TIMEOUT = 120;

	/**
	 * @var stdClass
	 */
	protected $options;

	/**
	 * @var RokBooster_Compressor_IStragety
	 */
	protected $strategy;


	/**
	 * if the background processing is happening
	 * @var bool
	 */
	protected $background = false;

	/**
	 * @param $subject
	 * @param $config
	 */
	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$app = JFactory::getApplication();
		if ($app->isAdmin()) {
			$lang = JFactory::getLanguage();
			$lang->load('plg_system_rokbooster', JPATH_ADMINISTRATOR);
		}
	}


	/**
	 * start buffer for background processing
	 */
	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		// Catch any output  from the frontend for the body
		if (!$app->isAdmin() && !$this->isDisabled() && $this->params->get('use_background_processing', 1) && (int)ini_get('output_buffering') == 0) {
			$this->background = true;
			//set default vales
			ob_start();
		}
	}

	protected function isDisabled()
	{
		if ($this->params->get('disable_for_ie', 0) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))) {
			return true;
		}
		return false;
	}

	/**
	 * @return mixed
	 */
	public function onBeforeCompileHead()
	{

		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();


		if ($app->isAdmin() || $doc->getType() != 'html') return;
		$this->setup();


		//no minify enabled
		if ($this->isDisabled() || (!$this->options->minify_html && !$this->options->minify_css && !$this->options->minify_js && !$this->options->inline_css && !$this->options->inline_js)
		) return;

		if ($this->options->scan_method == 'joomla') {
			$this->strategy->identify();
			if (!$this->background) {
				$this->strategy->process();
			}
			$this->strategy->populate();
		}
	}


	/**
	 * @return mixed
	 */
	function onAfterRender()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if ($app->isAdmin() || $doc->getType() != 'html') return;

		//no minify enabled
		if ($this->isDisabled() || (!$this->options->minify_html && !$this->options->minify_css && !$this->options->minify_js && !$this->options->inline_css && !$this->options->inline_js)
		) return;

		if ($this->options->scan_method != 'joomla') {
			$this->strategy->identify();
			if (!$this->background) {
				$this->strategy->process();
			}
			$this->strategy->populate();
		}
	}

	/**
	 * sets up vars from params / options
	 * platform specific
	 */
	protected function setup()
	{
		$this->options          = new stdClass();
		$doc                    = JFactory::getDocument();
		$this->options->lineEnd = $doc->_getLineEnd();
		$this->options->tab     = $doc->_getTab(); //is admin or doc type isn't html

		$this->options->cache_path = JPATH_CACHE . '/rokbooster/';
		if (!JFolder::exists($this->options->cache_path)) {
			JFolder::create($this->options->cache_path);
		}
		$this->options->cache_url = JURI::base(true) . '/cache/rokbooster/';
		$this->options->root_path = rtrim(JPATH_SITE, "/");
		$this->options->root_url  = rtrim(JURI::Root(), "/");

		//we could do $this->options = $this->params->toObject();
		//but that won't set defaults if plugin has never been saved

		//define variables
		$this->options->minify_css   = $this->params->get('minify_css', 1);
		$this->options->imported_css = $this->params->get('imported_css', 1);
		$this->options->inline_css   = $this->params->get('inline_css', 1);

		$this->options->minify_js = $this->params->get('minify_js', 1);
		$this->options->inline_js = $this->params->get('inline_js', 1);

		$this->options->minify_html = $this->params->get('minify_html', 1);

		$this->options->cache_time                = $this->params->get('cache_time', 60) * 60;
		$this->options->search_after              = $this->params->get('search_after', 0);
		$this->options->ignored_files             = $this->getIgnores($this->params->get('ignored_files'));
		$this->options->use_gzip                  = $this->params->get('use_gzip', 1);
		$this->options->use_background_processing = $this->params->get('use_background_processing', 1);
		$this->options->script_sort               = $this->params->get('script_sort', 'RokBooster_Compressor_Sort_ExternalOnTop');
		$this->options->style_sort                = $this->params->get('style_sort', 'RokBooster_Compressor_Sort_ExternalOnTop');
		$this->options->disable_for_ie            = $this->params->get('disable_for_ie', 0);
		$this->options->scan_method               = $this->params->get('scan_method', 'header');


		switch ($this->options->scan_method) {
			case 'joomla':
				$this->strategy = new RokBooster_Joomla_ListStrategy($this->options);
				break;
			case 'header':
			case 'body':
				$this->strategy = new RokBooster_Joomla_PhpQueryScanStrategy($this->options);
				break;
			default:
				break;
		}
	}


	/**
	 * @param $ignores_string
	 *
	 * @return array returns an array of specified ignore files
	 */
	protected function getIgnores($ignores_string)
	{
		$ignores = array();
		if (!empty($ignores_string)) {
			$tmp_ignores = explode("\n", $ignores_string);
			foreach ($tmp_ignores as $ignored_file) {
				$cleanFile = preg_replace('#[\r\n]#', '', $ignored_file);
				$filepath  = $this->getFileLink($cleanFile);
				if (file_exists($filepath)) {
					$ignores[] = $cleanFile;
					$ignores[] = $filepath;
					$ignores[] = $this->getFileLink($cleanFile, false);
				} else {
					$ignores[] = $ignored_file;
				}
			}
		}
		return $ignores;
	}

	/**
	 * @param string $link   original relative url
	 * @param bool   $isPath specify path or url, path is default
	 *
	 * @return string $filepath return requested link as a full url or full path
	 */
	protected function getFileLink($link, $isPath = true)
	{
		$uri = parse_url($this->options->root_url);
		if ($this->isExternal($link, $this->options->root_url)) return $link;
		$path = (isset($uri['path'])) ? $uri['path'] : '';
		$base = str_replace($path, '', $this->options->root_url);
		if ($link && $base && strpos($link, $base) !== false) $link = str_replace($base, "", $link);
		if ($link && $path && strpos($link, $path) !== false) $link = str_replace($path, "", $link);
		if (substr($link, 0, 1) != DS) $link = DS . $link;
		$filepath = ($isPath) ? $this->options->root_path . $link : $this->options->root_url . $link;
		return $filepath;
	}


	protected static function isExternal($url, $root_url)
	{

		$url_uri = parse_url($url);

		//if the url does not have a scheme must be internal
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
	 *
	 */
	function __destruct()
	{
		// Actual Generation happens here
		if ($this->background) {
			$rendered = trim(ob_get_clean());
			if (headers_sent() || (!headers_sent() && !isset($this->strategy))) {
				echo $rendered;
			} else {
				header("Connection: close\r\n");
				header('Content-Length: ' . strlen($rendered) . "\r\n");
				echo $rendered;
				// clean outside buffers;
				while (@ob_end_clean()) ;
				session_write_close();
				ignore_user_abort(true);
				ob_start();
				ob_end_flush(); // Strange behaviour, will not work
				flush(); // Unless both are called !
				while (@ob_end_clean()) ;
				if (!ini_get('safe_mode') && strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
					@set_time_limit(0);
				} else {
					error_log('RokBooster: PHP safe_mode is on or the set_time_limit function is disabled.  This can cause timeouts while processing a job if your max_execution_time is not set high enough');
				}

				$this->strategy->process();
			}
		}
	}


}