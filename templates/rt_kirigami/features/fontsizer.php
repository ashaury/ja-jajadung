<?php
/**
* @version   $Id: fontsizer.php 1513 2012-07-03 18:33:48Z kevin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureFontSizer extends GantryFeature {
    var $_feature_name = 'fontsizer';
    
	function init() {
        global $gantry;
		$fontsize = $gantry->get('font-size');
        $current_fontsize = $gantry->get('font-size-is');
        $font_sizes = array(
                0=>"xsmall",
                1=>"small",
                2=>"default",
                3=>"large",
                4=>"xlarge"
        );

		$current = array_search($current_fontsize, $font_sizes);
        if ($current !== false){
            switch ($fontsize){
                case 'smaller':
                    if ($current > 0) $current--;
                    break;
                case 'larger':
                    if ($current < count($font_sizes)-1) $current++;
                    break;
            }
            $gantry->set('font-size-is', $font_sizes[$current]);
        }
	}
	
	function render($position="") {
        global $gantry;
		ob_start();
		?>
        <div class="rt-block">
            <div class="rt-fontsizer">
                <span class="rt-fontsizer-desc"><?php echo JText::_('TEXT_SIZE'); ?></span>
                <div class="rt-fontsizer-buttons">
                    <a href="<?php echo JROUTE::_($gantry->addQueryStringParams($gantry->getCurrentUrl(array('reset-settings')),array('font-size'=>'smaller'))); ?>" title="<?php echo JText::_('DEC_FONT_SIZE'); ?>" class="smaller"><span class="button"></span></a>
                    <a href="<?php echo JROUTE::_($gantry->addQueryStringParams($gantry->getCurrentUrl(array('reset-settings')),array('font-size'=>'larger'))); ?>" title="<?php echo JText::_('INC_FONT_SIZE'); ?>" class="larger"><span class="button"></span></a>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}