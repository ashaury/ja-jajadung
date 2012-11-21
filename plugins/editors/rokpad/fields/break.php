<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldBreak extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Break';

	/**
	 * @return string
	 */
	protected function getLabel()
	{
		$label = $this->type;

		if (isset($this->element['label']) && !empty($this->element['label'])) {
			$label = JText::_((string)$this->element['label']);
			$css   = (string)$this->element['class'];
			return '<div class="rokpad-break ' . $css . '">' . $label . '</label>';
		} else {
			return;
		}

	}

	/**
	 * @return mixed
	 */
	protected function getInput()
	{
		return;
	}

}
