<?php
/**
* @version   $Id: styledeclaration.php 3393 2012-09-07 20:04:12Z kevin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureStyleDeclaration extends GantryFeature {
    var $_feature_name = 'styledeclaration';

    function isEnabled() {
        global $gantry;
        $menu_enabled = $this->get('enabled');

        if (1 == (int)$menu_enabled) return true;
        return false;
    }

function init() {
        global $gantry;
		$browser = $gantry->browser;

	// COLORCHOOSER
    $accentColor = new Color($gantry->get('main-accent'));
    $css = 'a, .active .sprocket-lists-title, .sprocket-lists li:hover .sprocket-lists-title, .sprocket-headlines-item a:hover, .sprocket-mosaic-title a:hover, #rt-footer a:hover, .sprocket-lists-item .readon span {color:'.$gantry->get('main-accent').';}'."\n";
    $css .= '.title:before, .items-leading h2:before, .component-content .item-page h2:before, .component-content .blog h2:before, .component-content .weblink-category h2:before, .items-row h2:before, .component-content .contact h2:before, .component-content .login h1:before, .sprocket-headlines-badge:before, .button, .readon, .readmore, button.validate, #member-profile a, #member-registration a, .formelm-buttons button, .sprocket-lists li:hover .indicator, .sprocket-lists li.active .indicator, .sprocket-lists .arrow:hover, .sprocket-lists-pagination li.active, .sprocket-headlines-navigation .arrow:hover, .sprocket-features-pagination li:hover span, .sprocket-features-arrows .arrow:hover, .rt-totop:hover, .sprocket-mosaic-order li:hover, .sprocket-mosaic-filter li:hover {background-color:'.$gantry->get('main-accent').';}'."\n";
    $css .= '.button:hover, .readon:hover, .readmore:hover, button.validate:hover, #member-profile a:hover, #member-registration a:hover, .formelm-buttons button:hover {background-color:'.$accentColor->darken('5%').';}'."\n";
    $css .= '#rt-header a, #rt-footer a {color:'.$accentColor->lighten('10%').';}'."\n";
    $css .= '.menutop li.root:hover, .menutop li.root.f-mainparent-itemfocus, .menutop li.root.active, .fusion-submenu-wrapper, .rt-splitmenu li:hover, .rt-splitmenu li.active, #roksearch_search_str, .rokajaxsearch .roksearch-wrapper input#roksearch_search_str.inputbox:focus, .title3 .title {border-color:'.$accentColor->lighten('5%').';}'."\n";
    $css .= '.login-fields #username:focus, .login-fields #password:focus, #contact-form dd input:focus, #contact-form dd textarea:focus, #modlgn-username:focus, #modlgn-passwd:focus, input#jform_email:focus, #rokajaxsearch .inputbox:focus, #member-registration dd input:focus, #search-searchword:focus, .finder .inputbox:focus {border: 1px solid '.$gantry->get('main-accent').'; box-shadow: inset 0 1px 3px '.$this->_RGBA($gantry->get('main-accent'), '0.3').', 0 0 8px '.$this->_RGBA($gantry->get('main-accent'), '0.6').';}'."\n";
    $css .= 'body ul.checkmark li::after, body ul.circle-checkmark li::before, body ul.square-checkmark li::before, body ul.circle-small li::after, body ul.circle li::after, body ul.circle-large li::after, .title5 .title::before, .sprocket-headlines-badge::after {border-color: '.$gantry->get('main-accent').';}'."\n";
    $css .= 'body ul.triangle-small li::after, body ul.triangle li::after, body ul.triangle-large li::after {border-color: transparent transparent transparent '.$gantry->get('main-accent').';}'."\n";

    // K2
    $css .= '#k2Container div.latestItemsCategory h2, #k2Container .itemListCategory h2, #k2Container .subCategory h2, div.k2TagCloudBlock a:hover, table.calendar tr td.calendarToday, div.k2CategoriesListBlock ul li.activeCategory > a, span.catItemAddLink a:hover, span.userItemAddLink a:hover, span.userItemEditLink a:hover, span.catItemEditLink a:hover, span.itemEditLink a:hover, #k2Container h2:before, table.calendar tr td.calendarDayName  {background-color:'.$accentColor->lighten('5%').';}'."\n";
    $css .= '#k2Container .userItemLinks, #k2Container .itemLinks, #k2Container .catItemLinks, #k2Container .latestItemLinks, #k2Container div.itemAuthorBlock, div.k2ItemsBlock ul li div.moduleItemTags  {border-color:'.$accentColor->lighten('5%').';}'."\n";
    $css .= '#k2Container .commentAuthorName {color:'.$gantry->get('main-accent').';}'."\n";
    $css .= ' #k2Container .button, .k2UserBlock .button, span.catItemAddLink a, span.userItemAddLink a, span.userItemEditLink a, span.catItemEditLink a, span.itemEditLink a, #k2Container .itemCommentsForm .button {background-color:'.$gantry->get('main-accent').';}'."\n";
    $css .= '#k2Container .button:hover, .k2UserBlock .button:hover, span.catItemAddLink a:hover, span.userItemAddLink a:hover, span.userItemEditLink a:hover, span.catItemEditLink a:hover, span.itemEditLink a:hover {background-color:'.$accentColor->darken('5%').';}'."\n";
    
    // Logo
    $css .= $this->buildLogo();

		// Static file
        if ($gantry->get('static-enabled')) {
            // do file stuff
            jimport('joomla.filesystem.file');
            $filename = $gantry->templatePath.DS.'css'.DS.'static-styles.css';

            if (file_exists($filename)) {
                if ($gantry->get('static-check')) {
                    //check to see if it's outdated

                    $md5_static = md5_file($filename);
                    $md5_inline = md5($css);

                    if ($md5_static != $md5_inline) {
                        JFile::write($filename, $css);
                    }
                }
            } else {
                // file missing, save it
                JFile::write($filename, $css);
            }
            // add reference to static file
            $gantry->addStyle('static-styles.css',99);

        } else {
            // add inline style
            $gantry->addInlineStyle($css);
        }

		// Style Inclusion
        $mainstyle = $gantry->get('main-body');
        if ($gantry->get('layout-mode')=="responsive") $gantry->addStyle('grid-12-responsive.css');
        $gantry->addStyle('gantry-core.css');
        $gantry->addStyle('joomla-core.css');
        $gantry->addStyle('main-'.$mainstyle.".css");
        $gantry->addStyle('utilities.css');
        if ($gantry->get('typography-enabled')) $gantry->addStyle('typography.css');
        if ($gantry->get('typography-enabled')) $gantry->addStyle('font-awesome.css');
        $gantry->addStyle('template.css');

        //third party
        if ($gantry->get('k2')) $gantry->addStyle('thirdparty-k2.css');
        if ($gantry->get('k2')) $gantry->addStyle('thirdparty-k2-'.$mainstyle.'.css');

        if ($gantry->get('layout-mode')=="responsive") $gantry->addStyle('mediaqueries.css');
        if ($gantry->get('layout-mode')=="960fixed") $gantry->addStyle('960fixed.css');
        if ($gantry->get('layout-mode')=="1200fixed") $gantry->addStyle('1200fixed.css'); 
	}

    function buildLogo(){
        global $gantry;

        if ($gantry->get('logo-type')!="custom") return "";

        $source = $width = $height = "";

        $logo = str_replace("&quot;", '"', str_replace("'", '"', $gantry->get('logo-custom-image')));
        $data = json_decode($logo);

        if (!$data){
            if (strlen($logo)) $source = $logo;
            else return "";
        } else {
            $source = $data->path;
        }

        if (substr($gantry->baseUrl, 0, strlen($gantry->baseUrl)) == substr($source, 0, strlen($gantry->baseUrl))){
            $file = JPATH_ROOT . DS . substr($source, strlen($gantry->baseUrl));
        } else {
            $file = JPATH_ROOT . DS . $source;
        }

        if (isset($data->width) && isset($data->height)){
            $width = $data->width;
            $height = $data->height;
        } else {
            $size = @getimagesize($file);
            $width = $size[0];
            $height = $size[1];
        }

        if (!preg_match('/^\//', $source))
        {
            $source = JURI::root(true).'/'.$source;
        }

        $output = "";
        $output .= "#rt-logo {background: url(".$source.") 50% 0 no-repeat !important;}"."\n";
        $output .= "#rt-logo {width: ".$width."px;height: ".$height."px;}"."\n";

        $file = preg_replace('/\//i', DS, $file);

        return (file_exists($file)) ?$output : '';
    }

    function _createGradient($direction, $from, $fromOpacity, $fromPercent, $to, $toOpacity, $toPercent){
        global $gantry;
        $browser = $gantry->browser;

        $fromColor = $this->_RGBA($from, $fromOpacity);
        $toColor = $this->_RGBA($to, $toOpacity);
        $gradient = $default_gradient = '';

        $default_gradient = 'background: linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';

        switch ($browser->engine) {
            case 'gecko':
                $gradient = ' background: -moz-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                break;

            case 'webkit':
                if ($browser->shortversion < '5.1'){

                    switch ($direction){
                        case 'top':
                            $from_dir = 'left top'; $to_dir = 'left bottom'; break;
                        case 'bottom':
                            $from_dir = 'left bottom'; $to_dir = 'left top'; break;
                        case 'left':
                            $from_dir = 'left top'; $to_dir = 'right top'; break;
                        case 'right':
                            $from_dir = 'right top'; $to_dir = 'left top'; break;
                    }
                    $gradient = ' background: -webkit-gradient(linear, '.$from_dir.', '.$to_dir.', color-stop('.$fromPercent.','.$fromColor.'), color-stop('.$toPercent.','.$toColor.'));';
                } else {
                    $gradient = ' background: -webkit-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                }
                break;

            case 'presto':
                $gradient = ' background: -o-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                break;

            case 'trident':
                if ($browser->shortversion >= '10'){
                    $gradient = ' background: -ms-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                } else if ($browser->shortversion <= '6'){
                    $gradient = $from;
                    $default_gradient = '';
                } else {

                    $gradient_type = ($direction == 'left' || $direction == 'right') ? 1 : 0;
                    $from_nohash = str_replace('#', '', $from);
                    $to_nohash = str_replace('#', '', $to);

                    if (strlen($from_nohash) == 3) $from_nohash = str_repeat(substr($from_nohash, 0, 1), 6);
                    if (strlen($to_nohash) == 3) $to_nohash = str_repeat(substr($to_nohash, 0, 1), 6);

                    if ($fromOpacity == 0 || $fromOpacity == '0' || $fromOpacity == '0%') $from_nohash = '00' . $from_nohash;
                    if ($toOpacity == 0 || $toOpacity == '0' || $toOpacity == '0%') $to_nohash = '00' . $to_nohash;

                    $gradient = " filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#".$to_nohash."', endColorstr='#".$from_nohash."',GradientType=".$gradient_type." );";

                    $default_gradient = '';

                }
                break;

            default:
                $gradient = $from;
                $default_gradient = '';
                break;
        }

        return  $default_gradient . $gradient;
    }

    function _HEX2RGB($hexStr, $returnAsString = false, $seperator = ','){
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
        $rgbArray = array();

        if (strlen($hexStr) == 6){
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3){
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false;
        }

        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
    }

    function _RGBA($hex, $opacity){
        return 'rgba(' . $this->_HEX2RGB($hex, true) . ','.$opacity.')';
    }

}
