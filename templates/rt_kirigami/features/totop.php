<?php
/**
* @version   $Id: totop.php 1513 2012-07-03 18:33:48Z kevin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureToTop extends GantryFeature {
    var $_feature_name = 'totop';

	function init() {
		global $gantry;
		
		if ($this->get('enabled')) {
			$gantry->addScript('gantry-totop.js');
		}
	}
	
	function render($position="") {
	    ob_start();
	    ?>
	    <div class="rt-block">
			<a href="#" class="rt-totop"></a>
		</div>
		<?php
	    return ob_get_clean();
	}
}