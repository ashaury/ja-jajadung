<?php
/**
* @version   $Id: social.php 1126 2012-06-21 19:23:15Z josh $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureSocial extends GantryFeature {
	var $_feature_name = 'social';

	function init(){
		global $gantry;
	}

	function render($position="") {
		ob_start();
		global $gantry;
		?>
		<div class="rt-social-buttons">
			<?php if ($gantry->get('social-facebook') != "") : ?>
			<a id="rt-facebook-btn" href="<?php echo $gantry->get('social-facebook'); ?>" target="_blank">
				<span class="rt-social-icon"><span class="rt-social-text">Follow via Facebook</span></span>
			</a>
			<?php endif; ?>
			<?php if ($gantry->get('social-twitter') != "") : ?>
			<a id="rt-twitter-btn" href="<?php echo $gantry->get('social-twitter'); ?>" target="_blank">
				<span class="rt-social-icon"><span class="rt-social-text">Follow via Twitter</span></span>
			</a>
			<?php endif; ?>
			<?php if ($gantry->get('social-google') != "") : ?>
			<a id="rt-google-btn" href="<?php echo $gantry->get('social-google'); ?>" target="_blank">
				<span class="rt-social-icon"><span class="rt-social-text">Follow via Google</span></span>
			</a>
			<?php endif; ?>
			<?php if ($gantry->get('social-rss') != "") : ?>
			<a id="rt-rss-btn" href="<?php echo $gantry->get('social-rss'); ?>" target="_blank">
				<span class="rt-social-icon"><span class="rt-social-text">Follow via RSS</span></span>
			</a>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
