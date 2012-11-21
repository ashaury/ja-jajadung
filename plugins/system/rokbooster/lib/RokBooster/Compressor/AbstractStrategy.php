<?php
/**
 * @version   $Id: AbstractStrategy.php 323 2012-04-27 07:19:52Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
abstract class RokBooster_Compressor_AbstractStrategy implements RokBooster_Compressor_IStragety
{

	/**
	 * @var object
	 */
	protected $options;

	/**
	 * @var string
	 */
	protected $current_css_url = '';

	/**
	 * @var string
	 */
	protected $current_key = '';

	/**
	 * @var array of ignored files and / or declarations
	 */
	protected $ignored = array();

	/**
	 * @var array
	 */
	protected $found = array();


	/**
	 * @var array
	 */
	protected $checksums = array();

	/**
	 * @var array
	 */
	protected $results = array();

	/**
	 * @var array
	 */
	protected $fileinfo = array();

	/**
	 * @var RokBooster_Compressor_ISort
	 */
	protected $script_file_sorter;
	/**
	 * @var RokBooster_Compressor_ISort
	 */
	protected $style_file_sorter;

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $inline_scripts = array();

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $inline_styles = array();

	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		$this->options            = $options;
		$script_sorter            = (string)$this->options->script_sort;
		$style_sorter             = (string)$this->options->style_sort;
		$this->script_file_sorter = new $script_sorter();
		$this->style_file_sorter  = new $style_sorter();
	}


	/**
	 * @param RokBooster_Compressor_File $file
	 */
	protected function addJsFile(RokBooster_Compressor_File &$file)
	{
		$this->script_file_sorter->addFile($file);
	}

	/**
	 * @param RokBooster_Compressor_File $file
	 */
	protected function addCssFile(RokBooster_Compressor_File &$file)
	{
		$this->style_file_sorter->addFile($file);
	}


	/**
	 * @param RokBooster_Compressor_FileGroup $files
	 */
	protected function processScriptFiles(RokBooster_Compressor_FileGroup &$files)
	{

		$content = '';
		//get links data
		foreach ($files as &$file) {
			/** @var $file RokBooster_Compressor_File */
			$tmp_content = RokBooster_Compressor_Processor_JSMin::_minify($file->getContent());
			$content .= $this->cleanEndLines($tmp_content) . ' ';
		}
		$files->setResult($content);
	}


	/**
	 * @param \RokBooster_Compressor_InlineGroup $inlinegroup
	 *
	 * @return string
	 */
	protected function processInlineScript(RokBooster_Compressor_InlineGroup &$inlinegroup)
	{
		$content = $this->cleanEndLines($inlinegroup->getContent());
		$inlinegroup->setResult(RokBooster_Compressor_Processor_JSMin::_minify($content));

	}


	/**
	 * @param RokBooster_Compressor_FileGroup $files
	 */
	protected function processStyleFiles(RokBooster_Compressor_FileGroup &$files)
	{
		$content = '';
		foreach ($files as $file) {
			/** @var $file RokBooster_Compressor_File */
			if ($file->getContent()) {
				$this->current_css_url = $file->getFile();
				if ($this->options->imported_css) {
					try {
						$compiled_content = RokBooster_Compressor_CssCompiler::compile($file->content, dirname($file->getPath()));
						$file->content    = $compiled_content;
					} catch (Exception $e) {
					}
				}
				$file->content = preg_replace('~@import\s?[\'"]([^\'"]+?)[\'"];~', '@import url("$1");', $file->content);
				$file->content = preg_replace_callback('~url\s?\([\'"]?(?![a-z]+:|/+)([^\'")]+)[\'"]?\)~i', array(
				                                                                                                 $this,
				                                                                                                 'correctUrl'
				                                                                                            ), $file->content);
				unset($this->current_css_url);
				$content .= $file->content . ' ';
			}
		}

		$files->setResult(RokBooster_Compressor_Processor_YUI::_minify($content));
	}

	/**
	 * @param \RokBooster_Compressor_InlineGroup $inlinegroup
	 *
	 * @return string
	 */
	protected function processInlineStyle(RokBooster_Compressor_InlineGroup &$inlinegroup)
	{
		$content = $inlinegroup->getContent();
		if ($this->options->imported_css) {
			try {
				$compiled_content = RokBooster_Compressor_CssCompiler::compile($content, $this->options->root_path);
				$content          = $compiled_content;
			} catch (Exception $e) {
			}
		}
		$content = $this->cleanEndLines($content);
		$inlinegroup->setResult(RokBooster_Compressor_Processor_YUI::_minify($content));

	}

	/**
	 * @param $matches
	 *
	 * @return string
	 */
	protected function correctUrl($matches)
	{
		if (!preg_match('~^(/|http)~', $matches[1])) {
			$cssRootPath = preg_replace('~/[^/]+\.css~', '/', $this->current_css_url);
			$imagePath   = $cssRootPath . $matches[1];
			$imagePath   = $this->normalize_path($imagePath);
			return 'url(\'' . $imagePath . '\')';
		} else {
			return $matches[0];
		}
	}

	/**
	 * This function is a proper replacement for realpath
	 * It will _only_ normalize the path and resolve indirections (.. and .)
	 * Normalization includes:
	 * - directiory separator is always /
	 * - there is never a trailing directory separator
	 *
	 * @param  $path
	 *
	 * @throws Exception
	 * @return String
	 */
	protected function normalize_path($path)
	{
		$parts = preg_split(":[\\\/]:", $path); // split on known directory separators

		// resolve relative paths
		for ($i = 0; $i < count($parts); $i += 1) {
			if ($parts[$i] === "..") { // resolve ..
				if ($i === 0) {
					throw new Exception("Cannot resolve path, path seems invalid: `" . $path . "`");
				}
				unset($parts[$i - 1]);
				unset($parts[$i]);
				$parts = array_values($parts);
				$i -= 2;
			} else if ($parts[$i] === ".") { // resolve .
				unset($parts[$i]);
				$parts = array_values($parts);
				$i -= 1;
			}
			if ($i > 0 && $parts[$i] === "") { // remove empty parts
				unset($parts[$i]);
				$parts = array_values($parts);
			}
		}
		return implode("/", $parts);
	}


	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function cleanEndLines($data)
	{
		$file_lines = explode("\n", $data);
		while (($line = array_pop($file_lines)) != null) {
			$clean_line = rtrim($line);
			if (strlen($clean_line) > 0) {
				$end_char = substr($line, strlen($clean_line), 1);
				array_push($file_lines, $line);
				if ($end_char != ';') {
					array_push($file_lines, ";");
				}
				break;
			}
		}
		return implode($file_lines, "\n");
	}

}
