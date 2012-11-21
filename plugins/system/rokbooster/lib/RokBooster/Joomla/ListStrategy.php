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
class RokBooster_Joomla_ListStrategy extends RokBooster_Joomla_AbstractStrategy
{

	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		parent::__construct($options);
	}


	/**
	 *
	 */
	public function identify()
	{
		if ($this->options->minify_js) {
			$this->identifyScriptFiles();
		}
		if ($this->options->minify_css) {
			$this->identifyStyleFiles();
		}
		if ($this->options->inline_js) {
			$this->identifyInlineScripts();
		}
		if ($this->options->inline_css) {
			$this->identifyInlineStyles();
		}
	}


	/**
	 *
	 */
	protected function identifyScriptFiles()
	{
		$doc = JFactory::getDocument();

		foreach ($doc->_scripts as $filename => $fileinfo) {

			$file = new RokBooster_Compressor_File($filename, $this->options->root_url, $this->options->root_path);
			$file->setMime($fileinfo['mime']);
			$file->setType('js');
			if ($fileinfo['defer']) $file->addAttribute('defer', 'defer');
			if ($fileinfo['async']) $file->addAttribute('async', 'async');
			if (in_array($file->file, $this->options->ignored_files)) {
				$file->setIgnored(true);
			}
			$this->script_file_sorter->addFile($file);
		}

		$script_file_groups = $this->script_file_sorter->getGroups();
		foreach ($script_file_groups as &$file_group) {
			if ($file_group->getStatus() != RokBooster_Compressor_IGroup::STATE_IGNORE && $this->isCacheExpired($file_group->getChecksum()) && !$this->isBeingRendered($file_group->getChecksum())) {
				$this->render_script_file_groups[] = $file_group;
				$this->setCurrentlyRendering($file_group->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	protected function identifyStyleFiles()
	{
		$doc = JFactory::getDocument();
		foreach ($doc->_styleSheets as $filename => $fileinfo) {
			$file = new RokBooster_Compressor_File($filename, $this->options->root_url, $this->options->root_path);
			$file->setType('css');
			$file->setMime($fileinfo['mime']);
			$file->setAttributes($fileinfo['attribs']);
			$file->addAttribute('media', $fileinfo['media']);
			if (in_array($file->file, $this->options->ignored_files)) {
				$file->setIgnored(true);
			}
			$this->style_file_sorter->addFile($file);
		}
		$file_groups = $this->style_file_sorter->getGroups();
		foreach ($file_groups as &$file_group) {
			if ($this->isCacheExpired($file_group->getChecksum()) && !$this->isBeingRendered($file_group->getChecksum())) {
				$this->render_style_file_groups[] = $file_group;
				$this->setCurrentlyRendering($file_group->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	protected function identifyInlineScripts()
	{
		$doc = JFactory::getDocument();
		foreach ($doc->_script as $mime => $content) {
			$inlineGroup = new RokBooster_Compressor_InlineGroup(RokBooster_Compressor_InlineGroup::STATE_INCLUDE, $mime);
			$inlineGroup->setContent($content);
			$this->inline_scripts[] = $inlineGroup;

			if ($this->isCacheExpired($inlineGroup->getChecksum()) && !$this->isBeingRendered($inlineGroup->getChecksum())) {
				$this->render_inline_scripts[] = $inlineGroup;
				$this->setCurrentlyRendering($inlineGroup->getChecksum());
			}
		}
	}


	/**
	 *
	 */
	protected function identifyInlineStyles()
	{
		$doc = JFactory::getDocument();
		foreach ($doc->_style as $mime => $content) {
			$inlineGroup = new RokBooster_Compressor_InlineGroup(RokBooster_Compressor_InlineGroup::STATE_INCLUDE, $mime);
			$inlineGroup->setContent($content);
			$this->inline_styles[] = $inlineGroup;
			$this->setCurrentlyRendering($inlineGroup->getChecksum());

			if ($this->isCacheExpired($inlineGroup->getChecksum()) && !$this->isBeingRendered($inlineGroup->getChecksum())) {
				$this->render_inline_styles[] = $inlineGroup;
				$this->setCurrentlyRendering($inlineGroup->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	public function populate()
	{
		if ($this->options->minify_js) {
			$this->populateScriptFiles();
		}
		if ($this->options->minify_css) {
			$this->populateStyleFiles();
		}
		if ($this->options->inline_js) {
			$this->populateInlineScripts();
		}
		if ($this->options->inline_css) {
			$this->populateInlineStyles();
		}
	}


	/**
	 *
	 */
	protected function populateScriptFiles()
	{
		$doc           = JFactory::getDocument();
		$doc->_scripts = array();
		foreach ($this->script_file_sorter->getGroups() as $group) {
			/** @var $group RokBooster_Compressor_FileGroup */
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				$doc->addScript($this->options->cache_url . $group->getChecksum() . '.php', $group->getMime(), array_key_exists('defer', $group->getAttributes()), array_key_exists('async', $group->getAttributes()));
			} else {
				foreach ($group as $file) {
					/** @var $file RokBooster_Compressor_File */
					$doc->addScript($file->getFile(), $file->getMime(), array_key_exists('defer', $file->getAttributes()), array_key_exists('async', $file->getAttributes()));
				}
			}
		}
	}

	/**
	 *
	 */
	protected function populateStyleFiles()
	{
		$doc               = JFactory::getDocument();
		$doc->_styleSheets = array();
		foreach ($this->style_file_sorter->getGroups() as $group) {
			/** @var $group RokBooster_Compressor_FileGroup */
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				$attribs = $group->getAttributes();
				$media   = $attribs['media'];
				unset($attribs['media']);
				$doc->addStyleSheet($this->options->cache_url . $group->getChecksum() . '.php', $group->getMime(), $media, $attribs);
			} else {
				foreach ($group as $file) {
					/** @var $file RokBooster_Compressor_File */
					$attribs = $file->getAttributes();
					$media   = null;
					if (array_key_exists('media', $attribs)) {
						$media = $attribs['media'];
						unset($attribs['media']);
					}
					$doc->addStyleSheet($file->getFile(), $group->getMime(), $media, $attribs);
				}
			}
		}
	}

	/**
	 *
	 */
	protected function populateInlineScripts()
	{

		$doc          = JFactory::getDocument();
		$doc->_script = array();
		foreach ($this->inline_scripts as $group) {
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				/** @var $group RokBooster_Compressor_InlineGroup */
				$doc->addScriptDeclaration(file_get_contents($this->options->cache_path . $group->getChecksum() . '.php', $group->getMime()));
			} else {
				$doc->addScriptDeclaration($group->getContent(), $group->getMime());
			}
		}
	}

	/**
	 *
	 */
	protected function populateInlineStyles()
	{
		$doc         = JFactory::getDocument();
		$doc->_style = array();
		foreach ($this->inline_styles as $group) {
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				/** @var $group RokBooster_Compressor_InlineGroup */
				$doc->addStyleDeclaration(file_get_contents($this->options->cache_path . $group->getChecksum() . '.php', $group->getMime()));
			} else {
				$doc->addStyleDeclaration($group->getContent(), $group->getMime());
			}
		}
	}
}
