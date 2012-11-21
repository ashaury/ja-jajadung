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
class RokBooster_Compressor_Sort_Ordered implements RokBooster_Compressor_ISort
{
	/**
	 * @var array
	 */
	protected $groups = array();

	/**
	 * @var
	 */
	protected $last_type;


	/**
	 * @var RokBooster_Compressor_FileGroup
	 */
	protected $current_group;


	/**
	 * @param RokBooster_Compressor_File $file
	 *
	 * @return mixed
	 */
	public function addFile(RokBooster_Compressor_File $file)
	{

		if ($file->isIgnored() || $file->isExternal()) {
			$current_type = RokBooster_Compressor_FileGroup::STATE_IGNORE;
		} else {
			$current_type = RokBooster_Compressor_FileGroup::STATE_INCLUDE;
		}

		if (!isset($this->current_group) || ($current_type != $this->current_group->getStatus() || $this->current_group->getMime() != $file->getMime() || $file->getAttributes() != $this->current_group->getAttributes())) {
			$this->last_type     = $current_type;
			$group               = new RokBooster_Compressor_FileGroup($current_type, $file->getMime(), $file->getAttributes());
			$this->groups[]      =& $group;
			$this->current_group =& $group;
		}
		$this->current_group->addItem($file);
	}

	/**
	 * @return RokBooster_Compressor_FileGroup[]
	 */
	public function getGroups()
	{
		return $this->groups;
	}
}
