<?php
/**
* @version   $Id: logo.php 1802 2012-07-20 17:46:18Z kevin $
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
class GantryFeaturelogo extends GantryFeature {
    var $_feature_name = 'logo';

    function isEnabled() {
        global $gantry;
        if (!isset($gantry->browser)) return $this->get('enabled');
        if ($gantry->browser->platform != 'iphone' && $gantry->browser->platform != 'android') return $this->get('enabled');

        $prefix = $gantry->get('template_prefix');
        $cookiename = $prefix.$gantry->browser->platform.'-switcher';
        $cookie = $gantry->retrieveTemp('platform', $cookiename);

        if ($cookie == 1 && $gantry->get($gantry->browser->platform.'-enabled')) return true;
        return $this->get('enabled');
    }

    function isInPosition($position){
        global $gantry;
        if (!isset($gantry->browser)) return $this->get('enabled');
        if ($gantry->browser->platform != 'iphone' && $gantry->browser->platform != 'android') return ($this->getPosition() == $position);

        $prefix = $gantry->get('template_prefix');
        $cookiename = $prefix.$gantry->browser->platform.'-switcher';
        $cookie = $gantry->retrieveTemp('platform', $cookiename);

        if ($cookie == 1 && $gantry->get($gantry->browser->platform.'-enabled') && ($position == 'mobile-top')) return true;

        return ($this->getPosition() == $position);
    }

    function render($position="") {
        global $gantry;

        ob_start();
        ?>
        <div class="rt-block logo-block">
            <a href="<?php echo $gantry->baseUrl; ?>" id="rt-logo"></a>
        </div>
        <?php
        return ob_get_clean();
    }
}