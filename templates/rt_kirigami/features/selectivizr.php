<?php
/**
 * @version   $Id: selectivizr.php 2116 2012-08-02 21:23:40Z kevin $
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');
/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureSelectivizr extends GantryFeature {
    var $_feature_name = 'selectivizr';
	
	function isInPosition($position) {
        return false;
    }
	function isOrderable(){
		return false;
	}
	
	function init() {
		global $gantry;
		
		if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion <= '8') {
			if ($this->get('enabled')) {
				$gantry->addScript('selectivizr-min.js');
			}
		}
	}
	
}